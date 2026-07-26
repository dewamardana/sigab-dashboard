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
                'latest' => $this->latestForLocation($location),
            ];
        });

        return view('public.index', compact('locations'));
    }

    /**
     * Halaman overview lokasi — kartu ringkas tiap device, berisi SEMUA
     * sensor (TMA, Hujan, dan sensor pendukung), tanpa grafik — untuk
     * riwayat & grafik, buka halaman device masing-masing.
     */
    public function show(Location $location): View
    {
        abort_unless($location->is_active, 404);

        $devices = $location->devices()->where('is_active', true)->with('sensorTypes')->get();

        $statusCounts = ['AMAN' => 0, 'SIAGA' => 0, 'BAHAYA' => 0];

        $deviceCards = $devices->map(function ($device) use (&$statusCounts) {
            $latest = $this->latestForDevice($device);
            if (isset($latest['status'], $statusCounts[$latest['status']])) {
                $statusCounts[$latest['status']]++;
            }

            $latestFull = SensorData::where('device_id', $device->id)->latest('recorded_at')->first();
            $secondary = $device->sensorTypes->where('is_core', false)->where('is_public', true)->map(fn($type) => [
                'code' => $type->code,
                'name' => $type->name,
                'unit' => $type->unit,
                'value' => $latestFull?->getReading($type->code),
            ])->values();

            return [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'name' => $device->name ?: $device->device_id,
                'latest' => $latest,
                'secondary' => $secondary,
                'threshold_tma_siaga' => $device->threshold_tma_siaga,
                'threshold_tma_bahaya' => $device->threshold_tma_bahaya,
                'threshold_hujan_siaga' => $device->threshold_hujan_siaga,
                'threshold_hujan_bahaya' => $device->threshold_hujan_bahaya,
                'tma_max' => (int) (ceil($device->threshold_tma_bahaya * 1.25 / 10) * 10),
                'hujan_max' => (int) (ceil($device->threshold_hujan_bahaya * 1.6 / 5) * 5),
            ];
        });

        return view('public.show', compact('location', 'deviceCards', 'statusCounts'));
    }

    /**
     * Halaman detail SATU device — TMA & Hujan dengan gauge masing-masing
     * (keduanya sama-sama menentukan status, bukan cuma TMA), sensor
     * pendukung, peta kecil, dan grafik riwayat terpisah per sensor.
     */
    public function device(Location $location, Device $device): View
    {
        abort_unless($location->is_active, 404);
        abort_unless($device->location_id === $location->id, 404);

        $history = SensorData::where('device_id', $device->id)
            ->latest('recorded_at')->limit(200)->get()->reverse()->values();

        $latest = $this->latestForDevice($device);
        $sensorTypes = $device->sensorTypes()->where('is_public', true)->orderByDesc('is_core')->get();

        // Skala maksimum gauge dihitung dari threshold BAHAYA milik device
        // sendiri (bukan angka tetap), supaya device dengan threshold
        // berbeda tetap menampilkan gauge yang proporsional.
        $tmaGaugeMax = (int) (ceil($device->threshold_tma_bahaya * 1.25 / 10) * 10);
        $hujanGaugeMax = (int) (ceil($device->threshold_hujan_bahaya * 1.6 / 5) * 5);

        return view('public.device', compact(
            'location', 'device', 'history', 'latest', 'sensorTypes', 'tmaGaugeMax', 'hujanGaugeMax'
        ));
    }

    /**
     * Pembacaan terakhir milik SATU device. Cache dulu (cepat) — kalau
     * kosong (mis. baru saja `cache:clear`/`optimize:clear`, atau server
     * baru restart), jatuh ke query database supaya data tidak "hilang"
     * padahal masih ada di tabel sensor_data. Cache diisi ulang supaya
     * request berikutnya tetap cepat.
     */
    private function latestForDevice(Device $device): ?array
    {
        $cached = Cache::get("device.{$device->id}.latest");
        if ($cached) {
            return $cached;
        }

        $record = SensorData::where('device_id', $device->id)->latest('recorded_at')->first();
        if (!$record) {
            return null;
        }

        $latest = [
            'status' => $record->status,
            'tma_cm' => $record->tma_cm,
            'hujan_mm' => $record->hujan_mm,
            'recorded_at' => $record->recorded_at->toIso8601String(),
        ];

        Cache::forever("device.{$device->id}.latest", $latest);

        return $latest;
    }

    /**
     * Sama seperti latestForDevice(), tapi untuk ringkasan tingkat lokasi
     * (dipakai kartu lokasi di homepage) — ambil dari device dengan
     * pembacaan paling baru di lokasi tersebut.
     */
    private function latestForLocation(Location $location): ?array
    {
        $cached = Cache::get("location.{$location->id}.latest");
        if ($cached) {
            return $cached;
        }

        $record = SensorData::whereHas('device', fn($q) => $q->where('location_id', $location->id))
            ->with('device')->latest('recorded_at')->first();
        if (!$record) {
            return null;
        }

        $latest = [
            'status' => $record->status,
            'tma_cm' => $record->tma_cm,
            'hujan_mm' => $record->hujan_mm,
            'device_id' => $record->device->device_id,
            'recorded_at' => $record->recorded_at->toIso8601String(),
        ];

        Cache::forever("location.{$location->id}.latest", $latest);

        return $latest;
    }
}
