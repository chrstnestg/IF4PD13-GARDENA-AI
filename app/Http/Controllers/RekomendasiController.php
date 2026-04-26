<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RekomendasiController extends Controller
{
    public function index()
    {
        $rekomendasiList = [
            [
                'id'          => 'nitrogen',
                'judul'       => 'Nitrogen (N)',
                'status'      => 'deficiency',
                'labelStatus' => 'Kekurangan',
                'nilaiSaatIni'=> 'EC: 1.2 mS/cm',
                'nilaiOptimal'=> '1.8–2.5 mS/cm',
                'deskripsi'   => 'Deep learning mendeteksi defisiensi Nitrogen sedang. Daun bawah mulai pucat dan pertumbuhan melambat.',
                'aksiList'    => [
                    'Tambahkan 12 ml Nutrient Grow ke reservoir 40 L',
                    'Jalankan pompa nutrisi selama 15 menit',
                    'Pantau ulang dalam 24 jam',
                    'Periksa warna daun setelah 48 jam',
                ],
                'kritis'      => true,
                'pesanKritis' => 'PERINGATAN KRITIS: EC rendah di Perangkat A (1.1 mS/cm). Tambahkan nutrisi sekarang!',
            ],
            [
                'id'          => 'ph',
                'judul'       => 'pH Level',
                'status'      => 'deficiency',
                'labelStatus' => 'Kekurangan',
                'nilaiSaatIni'=> 'pH: 5.3',
                'nilaiOptimal'=> 'pH: 5.8–6.5',
                'deskripsi'   => 'pH terlalu rendah (asam). Menghambat penyerapan Kalsium dan Magnesium.',
                'aksiList'    => [
                    'Tambahkan 5 ml pH Up solution',
                    'Aduk merata dan tunggu 10 menit',
                    'Ukur kembali pH setelah penyesuaian',
                    'Target pH ideal: 6.0–6.2',
                ],
                'kritis'      => false,
                'pesanKritis' => null,
            ],
        ];

        $healthScore = 68;
        $healthLabel = 'Sedang';

        $insightParagraf1 = 'Model deep learning mendeteksi penurunan EC secara konsisten selama 7 hari terakhir.';
        $insightParagraf2 = 'Prediksi 24–36 jam ke depan tanaman akan mengalami stres nutrisi jika tidak ditangani.';
        $insightTips = [
            'Jaga konsistensi suhu air dan kelembapan udara',
            'Pantau EC dan pH setiap hari',
            'Lakukan pemantauan rutin seperti biasa',
        ];
        $insightPrediksi = 'Dengan penanganan sekarang, tanaman berpotensi kembali optimal dalam 3 hari ke depan.';

        $chartLabels  = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $chartEc         = [96, 95, 97, 96, 98, 97, 98];
        $chartPh         = [94, 95, 96, 95, 97, 96, 97];
        $chartSuhu       = [93, 94, 95, 94, 96, 95, 96];
        $chartKelembapan = [92, 93, 94, 95, 96, 95, 97];

        return view('pages.rekomendasi', compact(
            'rekomendasiList', 'healthScore', 'healthLabel',
            'insightParagraf1', 'insightParagraf2', 'insightTips', 'insightPrediksi',
            'chartLabels', 'chartEc', 'chartPh', 'chartSuhu', 'chartKelembapan',
        ));
    }

    public function terapkan(Request $request)
    {
        return redirect()->route('rekomendasi')->with('success', 'Rekomendasi berhasil diterapkan!');
    }

    public function selesai(Request $request)
    {
        return redirect()->route('rekomendasi')->with('success', 'Tindakan dicatat sebagai sudah dilakukan.');
    }
}