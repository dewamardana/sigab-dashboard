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
     * terkini SEMUA sensor, termasuk yang privat), dikelompokkan per
     * lokasi. Klik kartu untuk riwayat & grafik lengkapnya (lihat method
     * device() di bawah). Hanya untuk superadmin (semua lokasi) dan
     * admin_lokasi (cuma lokasi yang ditugaskan ke dia).
     *
     * REVISI FUZZY ON-DEVICE: threshold_tma_/threshold_hujan_/tma_max/
     * hujan_max DIHAPUS - kolomnya sudah tidak ada, dan tidak ada lagi
     * gauge berbasis ambang. `readings` (sudah generik sejak awal, sudah
     * mencakup SEMUA sensor termasuk TMA/hujan) tidak berubah.
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
     * Detail SATU device — status besar + grid semua sensor (termasuk
     * privat), grafik riwayat terpisah per sensor, real-time lewat
     * channel privat admin.location.{id}.
     *
     * REVISI FUZZY ON-DEVICE: gauge TMA/Hujan berbasis threshold DIHAPUS.
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

        $latest = $latestFull ? [
            'status' => $latestFull->status,
            'recorded_at' => $latestFull->recorded_at->toIso8601String(),
        ] : null;

        return view('admin.monitoring-device', compact(
            'location', 'device', 'history', 'latest', 'latestFull', 'sensorTypes'
        ));
    }
}
