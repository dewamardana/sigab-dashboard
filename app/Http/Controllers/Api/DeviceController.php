<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;

class DeviceController extends Controller
{
    public function index(): JsonResponse
    {
        $devices = Device::where('is_active', true)
            ->with('location:id,name')
            ->select([
                'id',
                'device_id',
                'location_id',
                'threshold_tma_siaga',
                'threshold_tma_bahaya',
                'threshold_hujan_siaga',
                'threshold_hujan_bahaya',
                'telegram_chat_id',
            ])
            ->get()
            ->map(function ($device) {
                return [
                    'device_id' => $device->device_id,
                    'location_name' => $device->location->name ?? null,
                    'threshold_tma_siaga' => $device->threshold_tma_siaga,
                    'threshold_tma_bahaya' => $device->threshold_tma_bahaya,
                    'threshold_hujan_siaga' => $device->threshold_hujan_siaga,
                    'threshold_hujan_bahaya' => $device->threshold_hujan_bahaya,
                    'telegram_chat_id' => $device->telegram_chat_id,
                ];
            });

        return response()->json([
            'data' => $devices,
        ]);
    }
}
