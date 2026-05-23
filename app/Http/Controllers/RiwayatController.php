<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /* ──────────────────────────────────────────────
     | Halaman Riwayat Panen
     ────────────────────────────────────────────── */
    public function index(Request $request)
    {
        // Jumlah siklus total milik user (tidak terpengaruh filter)
        $totalSiklus = RiwayatPanen::where('id_user', Auth::id())
                                   ->max('siklus') ?? 0;

        // Query utama
        $query = RiwayatPanen::where('id_user', Auth::id())
                             ->orderByDesc('tanggal_panen');

        // Filter siklus
        if ($request->filled('siklus')) {
            $query->where('siklus', $request->siklus);
        }

        // Filter rentang tanggal
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_panen', [
                $request->dari,
                $request->sampai,
            ]);
        }

        $rows = $query->get();

        // Mapping label kualitas
        $kualitasMap = [
            'A+' => 'A+ (Istimewa)',
            'A'  => 'A (Sangat Baik)',
            'B+' => 'B+ (Baik)',
            'B'  => 'B (Cukup)',
        ];

        // Format data untuk view & modal Alpine.js
        $riwayatList = $rows->map(function (RiwayatPanen $r) use ($kualitasMap) {

            // Helper format sensor — tampilkan '-' kalau null
            $fmt = fn($val, $suffix) => $val !== null ? $val . $suffix : '-';

            return [
                'siklus'         => $r->siklus,
                'tanggal'        => $r->tanggal_panen->format('Y-m-d'),
                'tanggalLabel'   => $r->tanggal_panen->translatedFormat('j F Y'),
                'berat'          => number_format($r->berat_panen, 1) . ' kg',
                'jumlahIkat'     => $r->jumlah_ikat,
                'avgHealth'      => $r->avg_health,
                'kualitas'       => $r->kualitas,
                'kualitasLabel'  => $kualitasMap[$r->kualitas] ?? $r->kualitas,
                'catatan'        => \Str::limit($r->catatan, 40),
                'catatanLengkap' => $r->catatan ?? '-',
                'sensor'         => [
                    ['label' => 'EC',         'nilai' => $fmt($r->avg_tds,         ' mS/cm'), 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',         'nilai' => $fmt($r->avg_ph,          ''),        'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',   'nilai' => $fmt($r->avg_suhu,        '°C'),      'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan', 'nilai' => $fmt($r->avg_kelembapan,  '%'),       'icon' => 'bi-moisture'],
                ],
            ];
        })->values()->all();

        // Statistik
        $beratArr   = array_column($riwayatList, 'berat');
        $beratFloat = array_map(fn($b) => (float) $b, $beratArr);
        $terbaikRow = $rows->sortByDesc('berat_panen')->first();

        $stats = [
            'total'        => number_format(array_sum($beratFloat), 1) . ' Kg',
            'rata'         => count($beratFloat)
                                ? number_format(array_sum($beratFloat) / count($beratFloat), 1) . ' Kg'
                                : '0 Kg',
            'terbaik'      => $terbaikRow
                                ? number_format($terbaikRow->berat_panen, 1) . ' kg'
                                : '-',
            'terbaikLabel' => $terbaikRow
                                ? $terbaikRow->tanggal_panen->translatedFormat('F Y')
                                : '',
            'jumlah'       => $totalSiklus,
        ];

        return view('pages.riwayat', compact('riwayatList', 'stats'));
    }
}