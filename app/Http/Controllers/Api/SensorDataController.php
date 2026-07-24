<?php

namespace App\Http\Controllers\Api;

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
    private const CORE_FIELDS = ['device', 'tma_cm', 'hujan_mm', 'status'];

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'device' => ['required', 'string', 'exists:devices,device_id'],
            'tma_cm' => ['nullable', 'numeric'],
            'hujan_mm' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'in:AMAN,SIAGA,BAHAYA'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $device = Device::where('device_id', $request->input('device'))->firstOrFail();

        // Pisahkan field di luar CORE_FIELDS -> masuk kolom readings (JSON)
        // Ini yang membuat sensor apapun (sudah dikenal atau belum) tetap
        // bisa disimpan tanpa perlu ubah struktur tabel/kode.
        $readings = collect($request->all())
            ->except(self::CORE_FIELDS)
            ->filter(fn($value) => is_numeric($value) || is_string($value))
            ->toArray();

        $sensorData = SensorData::create([
            'device_id' => $device->id,
            'tma_cm' => $request->input('tma_cm'),
            'hujan_mm' => $request->input('hujan_mm'),
            'readings' => $readings,
            'status' => $request->input('status', 'AMAN'),
            'recorded_at' => now(),
        ]);

        // Siarkan ke Reverb — dashboard yang sedang terbuka otomatis update
        event(new SensorDataUpdated($sensorData));

        Cache::forever("location.{$device->location_id}.latest", [
            'status' => $sensorData->status,
            'tma_cm' => $sensorData->tma_cm,
            'hujan_mm' => $sensorData->hujan_mm,
            'device_id' => $device->device_id,
            'recorded_at' => $sensorData->recorded_at->toIso8601String(),
        ]);

        // Khusus per device, untuk halaman detail
        Cache::forever("device.{$device->id}.latest", [
            'status' => $sensorData->status,
            'tma_cm' => $sensorData->tma_cm,
            'hujan_mm' => $sensorData->hujan_mm,
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
