<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Location;
use App\Models\SensorData;
use Illuminate\View\View;

class AdminMonitoringController extends Controller
{
    /**
     * Dashboard real-time LENGKAP — kartu ringkas tiap device (angka
     * terkini semua sensor), dikelompokkan per lokasi. Klik kartu untuk
     * riwayat & grafik lengkapnya (lihat method device() di bawah).
     * Hanya untuk superadmin (semua lokasi) dan admin_lokasi (cuma lokasi
     * yang ditugaskan ke dia — sama persis dengan aturan otorisasi
     * channel privat di routes/channels.php).
     */
    public function index(): View
    {
        $user = auth()->user();

        $query = Location::where('is_active', true)->with(['devices' => function ($q) {
            $q->where('is_active', true)->with('sensorTypes');
        }]);

        if (!$user->hasRole('superadmin')) {
            $locationIds = $user->locations()->pluck('locations.id');
            $query->whereIn('id', $locationIds);
        }

        $locations = $query->get()->map(function ($location) {
            $devices = $location->devices->map(function ($device) {
                $latestFull = SensorData::where('device_id', $device->id)->latest('recorded_at')->first();

                $readings = $device->sensorTypes->sortByDesc('is_core')->map(fn($type) => [
                    'code' => $type->code,
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'is_core' => $type->is_core,
                    'value' => $latestFull?->getReading($type->code),
                ])->values();

                return [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'name' => $device->name ?: $device->device_id,
                    'status' => $latestFull?->status,
                    'recorded_at' => $latestFull?->recorded_at?->toIso8601String(),
                    'threshold_tma_siaga' => $device->threshold_tma_siaga,
                    'threshold_tma_bahaya' => $device->threshold_tma_bahaya,
                    'threshold_hujan_siaga' => $device->threshold_hujan_siaga,
                    'threshold_hujan_bahaya' => $device->threshold_hujan_bahaya,
                    'tma_max' => (int) (ceil($device->threshold_tma_bahaya * 1.25 / 10) * 10),
                    'hujan_max' => (int) (ceil($device->threshold_hujan_bahaya * 1.6 / 5) * 5),
                    'readings' => $readings,
                ];
            });

            return [
                'id' => $location->id,
                'name' => $location->name,
                'province' => $location->province,
                'devices' => $devices,
            ];
        });

        return view('admin.monitoring', compact('locations'));
    }

    /**
     * Detail SATU device — sama persis strukturnya dengan halaman device
     * publik (gauge TMA, bar Hujan, kartu sensor pendukung, grafik
     * riwayat terpisah per sensor), TAPI menyertakan SEMUA sensor
     * termasuk yang privat (baterai), dan real-time-nya lewat channel
     * privat admin.location.{id}.
     */
    public function device(Location $location, Device $device): View
    {
        abort_unless($location->is_active, 404);
        abort_unless($device->location_id === $location->id, 404);

        $user = auth()->user();
        if (!$user->hasRole('superadmin') && !$user->locations()->where('locations.id', $location->id)->exists()) {
            abort(403);
        }

        $history = SensorData::where('device_id', $device->id)
            ->latest('recorded_at')->limit(200)->get()->reverse()->values();

        $latestFull = $history->last();
        $sensorTypes = $device->sensorTypes()->orderByDesc('is_core')->get();

        $tmaGaugeMax = (int) (ceil($device->threshold_tma_bahaya * 1.25 / 10) * 10);
        $hujanGaugeMax = (int) (ceil($device->threshold_hujan_bahaya * 1.6 / 5) * 5);

        $latest = $latestFull ? [
            'status' => $latestFull->status,
            'tma_cm' => $latestFull->tma_cm,
            'hujan_mm' => $latestFull->hujan_mm,
            'recorded_at' => $latestFull->recorded_at->toIso8601String(),
        ] : null;

        return view('admin.monitoring-device', compact(
            'location', 'device', 'history', 'latest', 'sensorTypes', 'tmaGaugeMax', 'hujanGaugeMax'
        ));
    }
}
