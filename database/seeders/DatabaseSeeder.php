<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PerangkatIot;
use App\Models\DataSensor;
use App\Models\AnalisisAi;
use App\Models\RiwayatPanen;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. User dummy ──
        $user = User::firstOrCreate(
            ['email' => 'irene@gardena.ai'],
            [
                'name'     => 'Irene Kristi',
                'password' => bcrypt('password123'),
            ]
        );

        // ── 2. Perangkat IoT ──
        $perangkat = PerangkatIot::create([
            'id_user'      => $user->id,
            'nama_device'  => 'Perangkat A',
            'status_aktif' => true,
        ]);

        // ── 3. Data Sensor 7 hari terakhir ──
        for ($hari = 6; $hari >= 0; $hari--) {
            for ($jam = 0; $jam < 24; $jam++) {

                $sensor = DataSensor::create([
                    'id_device'   => $perangkat->id_device,
                    'ph'          => mt_rand(39, 41) / 10,   // 3.9–4.1 (rendah)
                    'suhu'        => mt_rand(180, 250) / 10, // 18–25°C
                    'ec_tds'      => mt_rand(300, 700),      // rendah
                    'kelembapan'  => mt_rand(60, 80),
                    'status_valid'=> true,
                    'dibaca_pada' => now()->subDays($hari)->setHour($jam),
                ]);

                // ── 4. Analisis AI untuk setiap sensor ──
                AnalisisAi::create([
                    'id_sensor'       => $sensor->id_sensor,
                    'kondisi_nutrisi' => 'deficiency',
                    'rekomendasi'     => json_encode([
                        'Tambahkan larutan nutrisi',
                        'Tambahkan pH Up',
                        'Pantau ulang dalam 24 jam',
                    ]),
                    'waktu_analisis'  => now()->subDays($hari)->setHour($jam),
                    'status_tindakan' => 'belum',
                ]);
            }
        }

        // ── 5. Riwayat Panen dummy ──
        $riwayatList = [
            [1, '2025-09-30', 10.5, 80,  80, 'B+', 1700, 6.0, 22.0, 65, 'Kualitas standar'],
            [2, '2025-10-25', 12.0, 92,  86, 'A',  1800, 6.2, 23.0, 64, 'Pertumbuhan stabil'],
            [3, '2025-11-18', 11.5, 89,  84, 'A',  1900, 6.0, 22.5, 62, 'Normal'],
            [4, '2025-12-10',  9.8, 76,  76, 'B',  1800, 6.5, 24.0, 60, 'Serangan hama ringan'],
            [5, '2026-01-15', 17.4, 135, 95, 'A+', 2100, 6.3, 22.0, 67, 'Panen terbaik'],
            [6, '2026-02-20', 13.2, 98,  88, 'A',  1900, 6.1, 23.5, 63, 'Hasil cukup memuaskan'],
            [7, '2026-03-28', 14.8, 112, 92, 'A',  2000, 6.2, 22.8, 65, 'Daun besar & segar'],
        ];

        foreach ($riwayatList as $i => [$siklus, $tgl, $berat, $ikat, $health, $kualitas, $tds, $ph, $suhu, $kelembapan, $catatan]) {
            RiwayatPanen::create([
                'id_user'        => $user->id,
                'id_device'      => $perangkat->id_device,
                'siklus'         => $siklus,
                'tanggal_panen'  => $tgl,
                'berat_panen'    => $berat,
                'jumlah_ikat'    => $ikat,
                'avg_health'     => $health,
                'kualitas'       => $kualitas,
                'avg_tds'        => $tds,
                'avg_ph'         => $ph,
                'avg_suhu'       => $suhu,
                'avg_kelembapan' => $kelembapan,
                'catatan'        => $catatan,
            ]);
        }
    }
}