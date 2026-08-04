<?php

namespace App\Events;

use App\Models\SensorData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Versi LENGKAP dari SensorDataUpdated — semua jenis sensor yang
 * terpasang di device (bukan cuma yang is_public), disiarkan HANYA ke
 * channel privat admin.location.{id}. Otorisasi channel ada di
 * routes/channels.php: superadmin bisa semua lokasi, admin_lokasi
 * cuma lokasi yang ditugaskan ke dia.
 */
class AdminSensorDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public SensorData $sensorData
    ) {
        $this->sensorData->load('device.location', 'device.sensorTypes');
    }

    public function broadcastOn(): array
    {
        $locationId = $this->sensorData->device->location_id;

        return [
            new PrivateChannel("admin.location.{$locationId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'sensor.updated';
    }

   
    public function broadcastWith(): array
    {
        $device = $this->sensorData->device;

        $readings = [];
        foreach ($device->sensorTypes as $type) {
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
