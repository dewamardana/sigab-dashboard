<?php

namespace Database\Seeders;

use App\Models\SensorType;
use Illuminate\Database\Seeder;

class SensorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['code' => 'tma_cm',      'name' => 'Tinggi Muka Air',  'unit' => 'cm',   'is_core' => true],
            ['code' => 'hujan_mm',    'name' => 'Curah Hujan',      'unit' => 'mm',   'is_core' => true],
            ['code' => 'suhu',        'name' => 'Suhu Udara',       'unit' => '°C',   'is_core' => false],
            ['code' => 'kelembapan',  'name' => 'Kelembapan',       'unit' => '%',    'is_core' => false],
            ['code' => 'angin_kmph',  'name' => 'Kecepatan Angin',  'unit' => 'km/h', 'is_core' => false],
            ['code' => 'baterai_v',   'name' => 'Tegangan Baterai', 'unit' => 'V',    'is_core' => false],
        ];

        foreach ($types as $type) {
            SensorType::firstOrCreate(['code' => $type['code']], $type);
        }

        $this->command->info('Katalog sensor_types berhasil diisi.');
    }
}
