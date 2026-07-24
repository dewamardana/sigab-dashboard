<?php

namespace App\Observers;

use App\Models\Device;
use App\Models\SystemEvent;
use App\Services\MqttReloadService;
use Illuminate\Support\Facades\Auth;

class DeviceObserver
{

    public function __construct(
        private MqttReloadService $mqttReload
    ) {}

    public function created(Device $device): void
    {
        $this->reloadAndLog('device_created', "Device baru didaftarkan: {$device->device_id}");
    }

    public function updated(Device $device): void
    {
        $this->reloadAndLog('device_updated', "Device diperbarui: {$device->device_id}");
    }

    public function deleted(Device $device): void
    {
        $this->reloadAndLog('device_deleted', "Device dihapus: {$device->device_id}");
    }

    private function reloadAndLog(string $eventType, string $description): void
    {
        $success = $this->mqttReload->triggerReload($eventType);

        SystemEvent::create([
            'event_type' => $eventType,
            'description' => $description . ($success ? '' : ' (peringatan: gagal publish MQTT reload, akan tertangkap fallback berkala)'),
            'user_id' => Auth::id(), // null jika dipicu dari Tinker/seeder/job tanpa user login
        ]);
    }
}
