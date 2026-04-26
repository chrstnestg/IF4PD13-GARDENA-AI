<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $riwayatList = [
            [
                'siklus'         => 8,
                'tanggal'        => '2026-03-28',
                'tanggalLabel'   => '28 Maret 2026',
                'berat'          => '14.8 kg',
                'jumlahIkat'     => 112,
                'avgHealth'      => 92,
                'kualitas'       => 'A',
                'kualitasLabel'  => 'A (Sangat Baik)',
                'catatan'        => 'Daun besar & segar',
                'catatanLengkap' => 'Daun besar & segar. Pertumbuhan sangat baik di siklus ini.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '2.0 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.2',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '26.5°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '65%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 7,
                'tanggal'        => '2026-02-20',
                'tanggalLabel'   => '20 Februari 2026',
                'berat'          => '13.2 kg',
                'jumlahIkat'     => 98,
                'avgHealth'      => 88,
                'kualitas'       => 'A',
                'kualitasLabel'  => 'A (Sangat Baik)',
                'catatan'        => 'Hasil cukup memuaskan',
                'catatanLengkap' => 'Hasil cukup memuaskan. Nutrisi stabil selama siklus.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '1.9 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.1',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '27.0°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '63%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 6,
                'tanggal'        => '2026-01-15',
                'tanggalLabel'   => '15 Januari 2026',
                'berat'          => '17.4 kg',
                'jumlahIkat'     => 135,
                'avgHealth'      => 95,
                'kualitas'       => 'A+',
                'kualitasLabel'  => 'A+ (Istimewa)',
                'catatan'        => 'Panen terbaik',
                'catatanLengkap' => 'Panen terbaik sepanjang tahun. Semua parameter optimal.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '2.1 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.3',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '26.0°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '67%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 5,
                'tanggal'        => '2025-12-10',
                'tanggalLabel'   => '10 Desember 2025',
                'berat'          => '9.8 kg',
                'jumlahIkat'     => 76,
                'avgHealth'      => 76,
                'kualitas'       => 'B',
                'kualitasLabel'  => 'B (Sangat Baik)',
                'catatan'        => 'Serangan hama ringan',
                'catatanLengkap' => 'Serangan hama ringan. Daun sawi besar, hijau mengkilap, dan renyah. Pertumbuhan cepat setelah penyesuaian nutrisi di minggu ke-3. Hasil memuaskan.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '1.8 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.5',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '26.2°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '60%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 4,
                'tanggal'        => '2025-11-18',
                'tanggalLabel'   => '18 November 2025',
                'berat'          => '11.5 kg',
                'jumlahIkat'     => 89,
                'avgHealth'      => 84,
                'kualitas'       => 'A',
                'kualitasLabel'  => 'A (Sangat Baik)',
                'catatan'        => 'Normal',
                'catatanLengkap' => 'Siklus berjalan normal tanpa gangguan berarti.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '1.9 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.0',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '27.1°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '62%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 3,
                'tanggal'        => '2025-10-25',
                'tanggalLabel'   => '25 Oktober 2025',
                'berat'          => '12.0 kg',
                'jumlahIkat'     => 92,
                'avgHealth'      => 86,
                'kualitas'       => 'A',
                'kualitasLabel'  => 'A (Sangat Baik)',
                'catatan'        => 'Pertumbuhan stabil',
                'catatanLengkap' => 'Pertumbuhan stabil. EC dan pH terjaga dengan baik.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '2.0 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.2',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '26.8°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '64%',       'icon' => 'bi-moisture'],
                ],
            ],
            [
                'siklus'         => 2,
                'tanggal'        => '2025-09-30',
                'tanggalLabel'   => '30 September 2025',
                'berat'          => '10.5 kg',
                'jumlahIkat'     => 80,
                'avgHealth'      => 80,
                'kualitas'       => 'B+',
                'kualitasLabel'  => 'B+ (Baik)',
                'catatan'        => 'Kualitas standar',
                'catatanLengkap' => 'Kualitas standar. Siklus perdana dengan hasil yang memuaskan.',
                'sensor'         => [
                    ['label' => 'EC',        'nilai' => '1.7 mS/cm', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',        'nilai' => '6.0',       'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',  'nilai' => '27.5°C',    'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan','nilai' => '61%',       'icon' => 'bi-moisture'],
                ],
            ],
        ];

        // Filter siklus
        if ($request->filled('siklus')) {
            $riwayatList = array_values(array_filter(
                $riwayatList,
                fn($r) => $r['siklus'] == $request->siklus
            ));
        }

        // Filter tanggal
        if ($request->filled('dari') && $request->filled('sampai')) {
            $riwayatList = array_values(array_filter(
                $riwayatList,
                fn($r) => $r['tanggal'] >= $request->dari && $r['tanggal'] <= $request->sampai
            ));
        }

        $semuaBerat = array_map(fn($r) => (float) $r['berat'], $riwayatList);
        $terbaikRow = collect($riwayatList)->sortByDesc(fn($r) => (float) $r['berat'])->first();

        $stats = [
            'total'        => round(array_sum($semuaBerat), 1) . ' Kg',
            'rata'         => count($semuaBerat) ? round(array_sum($semuaBerat) / count($semuaBerat), 1) . ' Kg' : '0 Kg',
            'terbaik'      => $terbaikRow ? $terbaikRow['berat'] : '-',
            'terbaikLabel' => $terbaikRow ? \Carbon\Carbon::parse($terbaikRow['tanggal'])->translatedFormat('F Y') : '',
            'jumlah'       => 8, // total siklus semua (bukan yang difilter)
        ];

        return view('pages.riwayat', compact('riwayatList', 'stats'));
    }
}