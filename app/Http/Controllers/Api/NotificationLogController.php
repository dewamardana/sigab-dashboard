<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\NotificationLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    /**
     * Dipanggil Node-RED setelah mencoba mengirim alert Telegram — baik
     * berhasil maupun gagal, supaya ada jejak audit lengkap kapan
     * warga/petugas pernah (atau gagal) diberi peringatan (Tahap 10).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device' => ['required', 'string', 'exists:devices,device_id'],
            'status' => ['required', 'string'],
            'message' => ['nullable', 'string'],
            'is_sent' => ['required', 'boolean'],
            'response' => ['nullable', 'string'],
        ]);

        $device = Device::where('device_id', $validated['device'])->firstOrFail();

        $log = NotificationLog::create([
            'device_id' => $device->id,
            'status' => $validated['status'],
            'message' => $validated['message'] ?? null,
            'is_sent' => $validated['is_sent'],
            'response' => $validated['response'] ?? null,
            'sent_at' => now(),
        ]);

        return response()->json(['message' => 'Log notifikasi tersimpan', 'id' => $log->id], 201);
    }
}
