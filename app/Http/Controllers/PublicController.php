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
     * sensor publik, tanpa grafik — untuk riwayat & grafik, buka halaman
     * device masing-masing.
     *
     * REVISI FUZZY ON-DEVICE: status sudah final dari device, jadi kartu
     * ini TIDAK lagi menghitung/menampilkan bar zona TMA & Hujan
     * berdasarkan threshold (kolomnya sudah dihapus dari tabel devices).
     * Semua sensor publik (termasuk TMA & hujan) sekarang tampil setara
     * lewat satu daftar generik `sensors`, diurutkan sensor "penentu
     * status" (is_core) di depan.
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

            $sensors = $device->sensorTypes
                ->where('is_public', true)
                ->sortByDesc('is_core')
                ->map(fn($type) => [
                    'code' => $type->code,
                    'name' => $type->name,
                    'unit' => $type->unit,
                    'is_core' => $type->is_core,
                    'value' => $latestFull?->getReading($type->code),
                ])
                ->values();

            return [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'name' => $device->name ?: $device->device_id,
                'latest' => $latest,
                'sensors' => $sensors,
            ];
        });

        return view('public.show', compact('location', 'deviceCards', 'statusCounts'));
    }

    /**
     * Halaman detail SATU device — status besar di atas + grid semua
     * sensor publik terkini, dan grafik riwayat terpisah per sensor.
     *
     * REVISI FUZZY ON-DEVICE: hero gauge TMA & bar Hujan berbasis
     * threshold DIHAPUS (kolom threshold sudah tidak ada) - status
     * sekarang ditunjukkan lewat badge besar (data dari field `status`,
     * final dari device), didukung sensor is_core (freeboard, intensitas
     * hujan, skor fuzzy) yang tampil menonjol di grid sensor.
     */
    public function device(Location $location, Device $device): View
    {
        abort_unless($location->is_active, 404);
        abort_unless($device->location_id === $location->id, 404);

        $history = SensorData::where('device_id', $device->id)
            ->latest('recorded_at')->limit(200)->get()->reverse()->values();

        $latest = $this->latestForDevice($device);
        $latestFull = $history->last();
        $sensorTypes = $device->sensorTypes()->where('is_public', true)->orderByDesc('is_core')->get();

        return view('public.device', compact(
            'location', 'device', 'history', 'latest', 'latestFull', 'sensorTypes'
        ));
    }

    /**
     * Pembacaan terakhir milik SATU device. Cache dulu (cepat) — kalau
     * kosong (mis. baru saja `cache:clear`/`optimize:clear`, atau server
     * baru restart), jatuh ke query database supaya data tidak "hilang"
     * padahal masih ada di tabel sensor_data. Cache diisi ulang supaya
     * request berikutnya tetap cepat.
     *
     * REVISI FUZZY ON-DEVICE: hanya status + recorded_at - tma_cm/
     * hujan_mm sudah bukan kolom khusus lagi, nilai sensor individual
     * selalu diambil langsung dari record SensorData terbaru di
     * pemanggil (lihat show()/device() di atas).
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
            'device_id' => $record->device->device_id,
            'recorded_at' => $record->recorded_at->toIso8601String(),
        ];

        Cache::forever("location.{$location->id}.latest", $latest);

        return $latest;
    }
}
