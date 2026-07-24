<?php

namespace App\Events;

use App\Models\SensorData;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SensorData $sensorData
    ) {
        // Eager load relasi supaya tidak query ulang saat broadcasting
        $this->sensorData->load('device.location');
    }

    /**
     * Channel yang akan menerima siaran event ini.
     * Dua channel: publik (ringkas, untuk halaman publik) dan
     * privat (lengkap, untuk dashboard admin).
     */
    public function broadcastOn(): array
    {
        $locationId = $this->sensorData->device->location_id;

        return [
            new Channel("location.{$locationId}"),
            new PrivateChannel("admin.location.{$locationId}"),
        ];
    }

    /**
     * Nama event di sisi frontend (dipakai Laravel Echo untuk listen).
     */
    public function broadcastAs(): string
    {
        return 'sensor.updated';
    }

    /**
     * Data ringkas untuk channel PUBLIK — hanya status & angka utama,
     * tidak menyertakan detail sensitif (baterai, dll).
     */
    public function broadcastWith(): array
    {
        return [
            'device_id' => $this->sensorData->device->device_id,
            'location_id' => $this->sensorData->device->location_id,
            'location_name' => $this->sensorData->device->location->name,
            'tma_cm' => $this->sensorData->tma_cm,
            'hujan_mm' => $this->sensorData->hujan_mm,
            'status' => $this->sensorData->status,
            'recorded_at' => $this->sensorData->recorded_at,
        ];
    }
}
