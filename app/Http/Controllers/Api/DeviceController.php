<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    /**
     * Dipanggil Node-RED saat startup & reload untuk mengisi cache
     * `device_list` (metadata saja - lokasi & Telegram). REVISI FUZZY
     * ON-DEVICE: threshold_tma_/threshold_hujan_* DIHAPUS dari respons
     * ini - Node-RED tidak lagi menghitung status, jadi tidak butuh
     * angka ambang apa pun dari sini.
     */
    public function index(): JsonResponse
    {
        $devices = Device::where('is_active', true)
            ->with('location:id,name')
            ->select(['id', 'device_id', 'location_id', 'telegram_chat_id'])
            ->get()
            ->map(function ($device) {
                return [
                    'device_id' => $device->device_id,
                    'location_name' => $device->location->name ?? null,
                    'telegram_chat_id' => $device->telegram_chat_id,
                ];
            });

        return response()->json([
            'data' => $devices,
        ]);
    }
}
