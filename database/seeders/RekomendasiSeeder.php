<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rekomendasi;

class RekomendasiSeeder extends Seeder
{
    public function run(): void
    {
        Rekomendasi::insert([
            [
                'nutrisi_id'    => 'ph',
                'judul'         => 'pH Level',
                'status'        => 'deficiency',
                'label_status'  => 'Terlalu Asam',
                'nilai_saat_ini'=> 'pH: 4.0',
                'nilai_optimal' => 'pH: 5.5–6.5',
                'deskripsi'     => 'pH terlalu rendah, menghambat penyerapan nutrisi.',
                'aksi_list'     => json_encode(['Tambahkan pH Up', 'Tunggu 10 menit', 'Ukur ulang']),
                'kritis'        => false,
                'pesan_kritis'  => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'nutrisi_id'    => 'tds',
                'judul'         => 'TDS / Nutrisi',
                'status'        => 'deficiency',
                'label_status'  => 'Kekurangan',
                'nilai_saat_ini'=> 'TDS: 600 ppm',
                'nilai_optimal' => '800–1400 ppm',
                'deskripsi'     => 'Kadar nutrisi di bawah optimal.',
                'aksi_list'     => json_encode(['Tambahkan larutan nutrisi', 'Pantau ulang dalam 24 jam']),
                'kritis'        => false,
                'pesan_kritis'  => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);
    }
}