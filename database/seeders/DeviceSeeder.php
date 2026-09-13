<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Location;
use App\Models\SensorType;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 2 device per lokasi, TANPA data sensor apa pun — cuma struktur
     * device-nya saja, siap langsung menerima data uji lewat
     * mosquitto_pub / Node-RED / endpoint /api/sensor-data.
     *
     * REVISI FUZZY ON-DEVICE: field threshold_tma_/threshold_hujan_
     * dihapus - device tidak lagi butuh angka ambang di database, karena
     * status sudah final dihitung microcontroller.
     **/
    public function run(): void
    {
        $sensorTypeIds = SensorType::pluck('id');

        $devicesByLocation = [
            'Sungai Ayung' => [
                ['device_id' => 'SIGAB-Bali-001', 'name' => 'Hulu Ayung'],
                ['device_id' => 'SIGAB-Bali-002', 'name' => 'Hilir Ayung'],
            ],
            'Sungai Code' => [
                ['device_id' => 'SIGAB-Jogja-001', 'name' => 'Hulu Code'],
                ['device_id' => 'SIGAB-Jogja-002', 'name' => 'Hilir Code'],
            ],
        ];

        foreach ($devicesByLocation as $locationName => $devices) {
            $location = Location::where('name', $locationName)->first();

            if (!$location) {
                $this->command->warn("Lokasi \"{$locationName}\" tidak ditemukan — jalankan LocationSeeder dulu.");
                continue;
            }

            foreach ($devices as $deviceData) {
                $device = Device::firstOrCreate(
                    ['device_id' => $deviceData['device_id']],
                    [
                        'location_id' => $location->id,
                        'name' => $deviceData['name'],
                        'is_active' => true,
                    ]
                );

                // Pasang semua jenis sensor supaya device langsung siap
                // dipakai uji coba, tanpa perlu setting manual dulu.
                $device->sensorTypes()->syncWithoutDetaching($sensorTypeIds);
            }
        }

        $this->command->info('4 perangkat dummy (2 per lokasi) berhasil dibuat, tanpa data sensor.');
    }
}
