<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataSensor;
use App\Models\AnalisisAi;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user     = \App\Models\User::first();

        // 1 data sensor kondisi pH Rendah
        $sensor = DataSensor::create([
            'id_device'    => $perangkat->id_device,
            'ph'           => 4.5,
            'suhu'         => 22.0,
            'ec_tds'       => 1200,
            'status_valid' => true,
            'dibaca_pada'  => now(),
        ]);

        AnalisisAi::create([
            'id_sensor'       => $sensor->id_sensor,
            'kondisi_nutrisi' => 'pH Rendah',
            'rekomendasi'     => json_encode([
                'Tambahkan larutan pH Up secara bertahap.',
                'Target pH antara 5.5 hingga 6.5.',
            ]),
            'waktu_analisis'  => now(),
            'status_tindakan' => 'belum',
        ]);
    }
}