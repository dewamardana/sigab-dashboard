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
        $this->sensorData->load('device.location', 'device.sensorTypes');
    }

    /**
     * Channel PUBLIK saja — data lengkap semua sensor DIBOLEHKAN publik
     * (is_public=true di sensor_types), lewat event terpisah
     * App\Events\AdminSensorDataUpdated ke channel privat
     * admin.location.{id} yang menyertakan SEMUA sensor termasuk yang
     * privat (mis. baterai).
     */
    public function broadcastOn(): array
    {
        $locationId = $this->sensorData->device->location_id;

        return [
            new Channel("location.{$locationId}"),
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
     * Data untuk channel PUBLIK — status + semua sensor yang ditandai
     * is_public=true (dihitung dinamis dari database, bukan nama sensor
     * yang di-hardcode). REVISI FUZZY ON-DEVICE: tidak ada lagi top-level
     * tma_cm/hujan_mm terpisah - keduanya sekarang cuma entri biasa di
     * dalam `readings`, sama seperti sensor lain.
     */
    public function broadcastWith(): array
    {
        $device = $this->sensorData->device;

        $readings = [];
        foreach ($device->sensorTypes->where('is_public', true) as $type) {
            $readings[$type->code] = $this->sensorData->getReading($type->code);
        }

        return [
            'device_id' => $device->device_id,
            'location_id' => $device->location_id,
            'location_name' => $device->location->name,
            'status' => $this->sensorData->status,
            'recorded_at' => $this->sensorData->recorded_at,
            'readings' => $readings,
        ];
    }
}
