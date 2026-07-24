<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;
use PhpMqtt\Client\MqttClient;

class MqttReloadService
{
    private const RELOAD_TOPIC = 'sigab/system/reload_devices';

    /**
     * Publish sinyal reload ke Node-RED, memberitahu bahwa daftar
     * device/threshold telah berubah dan cache perlu diperbarui.
     *
     * Dipanggil otomatis oleh DeviceObserver setiap kali device
     * dibuat, diubah, atau dihapus — menghilangkan kebutuhan
     * menjalankan mosquitto_pub manual.
     */
    public function triggerReload(string $reason = 'device_updated'): bool
    {
        // Client ID unik per panggilan agar tidak bentrok jika ada
        // beberapa proses PHP mempublish bersamaan (mis. saat trafik tinggi)
        $clientId = config('services.mqtt.client_id') . '-' . uniqid();

        $mqtt = new MqttClient(
            config('services.mqtt.host'),
            config('services.mqtt.port'),
            $clientId
        );

        try {
            $settings = (new ConnectionSettings)
                ->setConnectTimeout(5)
                ->setSocketTimeout(5);

            $mqtt->connect($settings);
            $mqtt->publish(self::RELOAD_TOPIC, $reason, 0);
            $mqtt->disconnect();

            return true;
        } catch (MqttClientException $e) {
            // Gagal publish TIDAK BOLEH menghentikan proses utama
            // (mis. gagal simpan device) — cukup dicatat sebagai log.
            // Node-RED tetap punya fallback reload berkala 15 menit
            // (Tahap 6) sebagai jaring pengaman.
            Log::warning('Gagal publish reload MQTT: ' . $e->getMessage());

            return false;
        }
    }
}
