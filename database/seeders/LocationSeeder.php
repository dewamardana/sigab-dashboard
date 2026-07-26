<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Sungai Ayung',
                'description' => 'Sungai Ayung, salah satu sungai terpanjang di Bali, melintasi Kabupaten Badung dan Gianyar.',
                'latitude' => -8.5500000,
                'longitude' => 115.2600000,
                'province' => 'Bali',
                'is_active' => true,
            ],
            [
                'name' => 'Sungai Code',
                'description' => 'Sungai Code yang melintasi Kota Yogyakarta, salah satu titik rawan banjir saat musim hujan.',
                'latitude' => -7.7900000,
                'longitude' => 110.3700000,
                'province' => 'DI Yogyakarta',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate(['name' => $location['name']], $location);
        }

        $this->command->info('2 lokasi dummy berhasil dibuat.');
    }
}
