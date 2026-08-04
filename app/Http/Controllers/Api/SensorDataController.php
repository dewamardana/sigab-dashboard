<?php

namespace App\Http\Controllers\Api;

use App\Events\AdminSensorDataUpdated;
use App\Events\SensorDataUpdated;
use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class SensorDataController extends Controller
{
    /**
     * REVISI FUZZY ON-DEVICE (Agustus 2026)
     * ============================================================
     * `tma_cm` & `hujan_mm` BUKAN lagi field inti — sekarang cuma data
     * monitoring biasa, setara suhu/kelembapan/dst, dan otomatis masuk
     * `readings` seperti sensor lainnya. Satu-satunya field inti yang
     * tersisa adalah `device` (identitas) dan `status` (vonis akhir dari
     * fuzzy di microcontroller, field ini yang dipakai untuk badge,
     * routing notifikasi, filter laporan, dst).
     */
    private const CORE_FIELDS = ['device', 'status'];

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device' => ['required', 'string', 'exists:devices,device_id'],
            'status' => ['nullable', 'string', 'in:AMAN,SIAGA,BAHAYA'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $request->input('device'))->firstOrFail();

        // Pisahkan field di luar CORE_FIELDS -> masuk kolom readings (JSON).
        // Sensor apa pun (sudah dikenal atau belum, termasuk TMA, hujan,
        // freeboard, skor fuzzy) otomatis tersimpan tanpa perlu ubah kode.
        // Catatan: is_bool() DITAMBAHKAN ke filter ini - sebelumnya field
        // boolean (mis. level_kritis) diam-diam TERBUANG karena
        // is_numeric()/is_string() sama-sama false untuk boolean di PHP.
        $readings = collect($request->all())
            ->except(self::CORE_FIELDS)
            ->filter(fn($value) => is_numeric($value) || is_string($value) || is_bool($value))
            ->toArray();

        $sensorData = SensorData::create([
            'device_id' => $device->id,
            'readings' => $readings,
            'status' => $request->input('status', 'AMAN'),
            'recorded_at' => now(),
        ]);

        // Siarkan ke Reverb — dashboard yang sedang terbuka otomatis update.
        // Dua event: publik (ringkas, ke channel biasa) dan admin (lengkap
        // semua sensor, ke channel privat admin.location.{id}).
        event(new SensorDataUpdated($sensorData));
        event(new AdminSensorDataUpdated($sensorData));

        // Cache ringkas HANYA status + waktu - dipakai utk badge status &
        // hitung jumlah AMAN/SIAGA/BAHAYA di halaman overview. Nilai
        // sensor individual (freeboard, TMA, dst) selalu diambil langsung
        // dari record SensorData terbaru saat halaman detail dibuka, jadi
        // tidak perlu ikut disimpan di cache ringkas ini.
        Cache::forever("location.{$device->location_id}.latest", [
            'status' => $sensorData->status,
            'device_id' => $device->device_id,
            'recorded_at' => $sensorData->recorded_at->toIso8601String(),
        ]);

        Cache::forever("device.{$device->id}.latest", [
            'status' => $sensorData->status,
            'recorded_at' => $sensorData->recorded_at->toIso8601String(),
        ]);

        return response()->json([
            'message' => 'Data sensor berhasil disimpan.',
            'data' => [
                'id' => $sensorData->id,
                'device_id' => $device->device_id,
                'status' => $sensorData->status,
            ],
        ], 201);
    }
}
