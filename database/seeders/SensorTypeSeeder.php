<?php

namespace Database\Seeders;

use App\Models\SensorType;
use Illuminate\Database\Seeder;

class SensorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * ============================================================
     * REVISI FUZZY ON-DEVICE (Agustus 2026)
     * ============================================================
     * Sebelumnya `is_core` berarti "kolom khusus di sensor_data karena
     * jadi penentu status" (cuma tma_cm & hujan_mm). Sekarang SEMUA
     * sensor - termasuk TMA & hujan - lewat kolom generik `readings`,
     * karena status tidak lagi dihitung dari nilai mentahnya di Laravel/
     * Node-RED, melainkan sudah final dikirim device lewat field
     * `status`. `is_core` sekarang murni penanda TAMPILAN: sensor yang
     * benar-benar dipakai microcontroller untuk menghitung fuzzy
     * (freeboard, intensitas hujan, skor hasil fuzzy) ditandai core
     * supaya tampil menonjol ("Penentu Status") di halaman device -
     * TMA(cm) & akumulasi hujan sekarang jadi info pendukung biasa.
     */
    public function run(): void
    {
        $types = [
            // --- Core: sensor yang benar-benar dipakai fuzzy di device ---
            ['code' => 'freeboard_m',       'name' => 'Freeboard (Jarak ke Tebing Kritis)', 'unit' => 'm',      'is_core' => true,  'is_public' => true],
            ['code' => 'hujan_intensitas',  'name' => 'Intensitas Hujan',                    'unit' => 'mm/jam', 'is_core' => true,  'is_public' => true],
            ['code' => 'status_skor',       'name' => 'Skor Risiko Fuzzy',                   'unit' => 'skor',   'is_core' => true,  'is_public' => true],

            // --- Info pendukung (dulu core, sekarang setara sensor lain) ---
            ['code' => 'tma_cm',            'name' => 'Tinggi Muka Air (referensi)',         'unit' => 'cm',     'is_core' => false, 'is_public' => true],
            ['code' => 'hujan_mm',          'name' => 'Akumulasi Hujan',                     'unit' => 'mm',     'is_core' => false, 'is_public' => true],
            ['code' => 'hujan_kategori',    'name' => 'Kategori Hujan',                      'unit' => null,     'is_core' => false, 'is_public' => true],
            ['code' => 'level_kritis',      'name' => 'Level Kritis (Float Switch)',         'unit' => null,     'is_core' => false, 'is_public' => true],

            // --- Sensor pendukung lama, tidak berubah ---
            ['code' => 'suhu',              'name' => 'Suhu Udara',                          'unit' => '°C',     'is_core' => false, 'is_public' => true],
            ['code' => 'kelembapan',        'name' => 'Kelembapan',                          'unit' => '%',      'is_core' => false, 'is_public' => true],
            ['code' => 'angin_kmph',        'name' => 'Kecepatan Angin',                     'unit' => 'km/h',   'is_core' => false, 'is_public' => true],
            ['code' => 'baterai_v',         'name' => 'Tegangan Baterai',                    'unit' => 'V',      'is_core' => false, 'is_public' => false],
        ];

        foreach ($types as $type) {
            SensorType::updateOrCreate(['code' => $type['code']], $type);
        }

        $this->command->info('Katalog sensor_types berhasil diisi (revisi fuzzy on-device, ' . count($types) . ' jenis sensor).');
    }
}
