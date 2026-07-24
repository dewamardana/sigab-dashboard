<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Location;
use App\Models\SensorData;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function index(): View
    {
        $locations = Location::where('is_active', true)->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'province' => $location->province,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'device_count' => $location->devices()->where('is_active', true)->count(),
                'latest' => Cache::get("location.{$location->id}.latest"),
            ];
        });

        return view('public.index', compact('locations'));
    }

    /**
     * Halaman overview lokasi — grafik GABUNGAN per jenis sensor
     * (semua device dalam satu grafik, dibedakan lewat legenda nama
     * device), TANPA peta. Untuk detail satu device, klik card device.
     */
    public function show(Location $location): View
    {
        abort_unless($location->is_active, 404);

        $devices = $location->devices()->where('is_active', true)->with('sensorTypes')->get();

        // Kumpulkan semua jenis sensor yang dipakai device-device di
        // lokasi ini (union, tanpa duplikat), sensor inti (TMA/Hujan)
        // ditampilkan lebih dulu
        $sensorTypesUsed = $devices->flatMap(fn($d) => $d->sensorTypes)
            ->unique('id')
            ->sortByDesc('is_core')
            ->values();

        $charts = $sensorTypesUsed->map(function ($type) use ($devices) {
            $series = $devices->filter(fn($d) => $d->sensorTypes->contains('id', $type->id))
                ->map(function ($device) use ($type) {
                    $history = SensorData::where('device_id', $device->id)
                        ->latest('recorded_at')->limit(200)->get()->reverse()->values();

                    $points = $history->map(fn($h) => [
                        'x' => $h->recorded_at->timestamp * 1000,
                        'y' => $h->getReading($type->code),
                    ])->filter(fn($p) => $p['y'] !== null)->values();

                    return [
                        'device_id' => $device->device_id,
                        'name' => $device->name ?: $device->device_id,
                        'data' => $points,
                    ];
                })->values();

            return [
                'code' => $type->code,
                'name' => $type->name,
                'unit' => $type->unit,
                'is_core' => $type->is_core,
                'series' => $series,
            ];
        });

        $deviceCards = $devices->map(fn($device) => [
            'id' => $device->id,
            'device_id' => $device->device_id,
            'name' => $device->name ?: $device->device_id,
            'latest' => Cache::get("device.{$device->id}.latest"),
        ]);

        return view('public.show', compact('location', 'charts', 'deviceCards'));
    }

    /**
     * Halaman detail SATU device — peta lokasi + seluruh grafik sensor
     * milik device ini saja.
     */
    public function device(Location $location, Device $device): View
    {
        abort_unless($location->is_active, 404);
        abort_unless($device->location_id === $location->id, 404);

        $history = SensorData::where('device_id', $device->id)
            ->latest('recorded_at')->limit(200)->get()->reverse()->values();

        $latest = Cache::get("device.{$device->id}.latest");
        $sensorTypes = $device->sensorTypes()->orderByDesc('is_core')->get();

        return view('public.device', compact('location', 'device', 'history', 'latest', 'sensorTypes'));
    }
}
