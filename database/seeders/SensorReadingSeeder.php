<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorReading;

class SensorReadingSeeder extends Seeder
{
    public function run(): void
    {
        for ($hari = 6; $hari >= 0; $hari--) {
            for ($jam = 0; $jam < 24; $jam++) {
                SensorReading::create([
                    'perangkat'   => 'Perangkat A',
                    'tds'         => mt_rand(800, 1400),          // ppm
                    'ph'          => mt_rand(39, 41) / 10, // 5.5 - 6.5
                    'suhu'        => mt_rand(180, 250) / 10,   // 18 - 25°C
                    'kelembapan'  => mt_rand(60, 80),             // %
                    'dibaca_pada' => now()->subDays($hari)->setHour($jam),
                ]);
            }
        }
    }
}