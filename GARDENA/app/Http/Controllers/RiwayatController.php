<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPanen;
use App\Models\PerangkatIot;
use App\Models\DataSensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /* ─────────────────────────────────────────
     | Halaman Riwayat Panen
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        $totalSiklus = RiwayatPanen::where('id_user', Auth::id())
                                   ->max('siklus') ?? 0;

        $query = RiwayatPanen::where('id_user', Auth::id())
                             ->orderByDesc('tanggal_panen');

        if ($request->filled('siklus')) {
            $query->where('siklus', $request->siklus);
        }

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_panen', [
                $request->dari,
                $request->sampai,
            ]);
        }

        $rows = $query->get();

        $kualitasMap = [
            'A+' => 'A+ (Istimewa)',
            'A'  => 'A (Sangat Baik)',
            'B+' => 'B+ (Baik)',
            'B'  => 'B (Cukup)',
        ];

        $riwayatList = $rows->map(function (RiwayatPanen $r) use ($kualitasMap) {
            $fmt = fn($val, $suffix) => $val !== null ? $val . $suffix : '-';

            return [
                'id'             => $r->id,
                'siklus'         => $r->siklus,
                'tanggal'        => $r->tanggal_panen->format('Y-m-d'),
                'tanggalLabel'   => $r->tanggal_panen->translatedFormat('j F Y'),
                'berat'          => number_format($r->berat_panen, 1) . ' kg',
                'beratRaw'       => $r->berat_panen,
                'jumlahIkat'     => $r->jumlah_ikat,
                'avgHealth'      => $r->avg_health,
                'kualitas'       => $r->kualitas,
                'kualitasLabel'  => $kualitasMap[$r->kualitas] ?? $r->kualitas,
                'catatan'        => \Str::limit($r->catatan, 40),
                'catatanLengkap' => $r->catatan ?? '-',
                'sensor'         => [
                    ['label' => 'TDS',        'nilai' => $fmt($r->avg_tds, ' ppm'), 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'pH',         'nilai' => $fmt($r->avg_ph,         ''),        'icon' => 'bi-droplet-fill'],
                    ['label' => 'Suhu Air',   'nilai' => $fmt($r->avg_suhu,       '°C'),      'icon' => 'bi-thermometer-half'],
                    ['label' => 'Kelembapan', 'nilai' => $fmt($r->avg_kelembapan, '%'),       'icon' => 'bi-moisture'],
                ],
            ];
        })->values()->all();

        $beratFloat = array_map(fn($b) => (float) $b, array_column($riwayatList, 'berat'));
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

    /* ─────────────────────────────────────────
     | Form Tambah Panen Manual
     ───────────────────────────────────────── */
    public function tambah()
    {
        $siklus = RiwayatPanen::where('id_user', Auth::id())->max('siklus') ?? 0;

        return view('pages.riwayat-tambah', [
            'siklusBerikutnya' => $siklus + 1,
        ]);
    }

    /* ─────────────────────────────────────────
     | Simpan Panen Manual
     ───────────────────────────────────────── */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_panen' => 'required|date',
            'berat_panen'   => 'required|numeric|min:0.1',
            'jumlah_ikat'   => 'required|integer|min:1',
            'catatan'       => 'nullable|string|max:500',
        ]);

        $siklus    = RiwayatPanen::where('id_user', Auth::id())->max('siklus') ?? 0;
        $perangkat = PerangkatIot::where('id_user', Auth::id())->first();

        $avgSensor = $perangkat
            ? DataSensor::where('id_device', $perangkat->id_device)
                ->where('dibaca_pada', '>=', now()->subDays(30))
                ->selectRaw('AVG(ec_tds) as tds, AVG(ph) as ph, AVG(suhu) as suhu, AVG(kelembapan) as kelembapan')
                ->first()
            : null;

        $health   = $this->hitungHealth($avgSensor);
        $kualitas = match(true) {
            $health >= 90 => 'A+',
            $health >= 80 => 'A',
            $health >= 70 => 'B+',
            default       => 'B',
        };

        RiwayatPanen::create([
            'id_user'        => Auth::id(),
            'id_device'      => $perangkat?->id_device,
            'siklus'         => $siklus + 1,
            'tanggal_panen'  => $request->tanggal_panen,
            'berat_panen'    => $request->berat_panen,
            'jumlah_ikat'    => $request->jumlah_ikat,
            'avg_health'     => $health,
            'kualitas'       => $kualitas,
            'avg_tds'        => $avgSensor?->tds,
            'avg_ph'         => $avgSensor?->ph,
            'avg_suhu'       => $avgSensor?->suhu,
            'avg_kelembapan' => $avgSensor?->kelembapan,
            'catatan'        => $request->catatan,
        ]);

        return redirect()->route('riwayat')
            ->with('success', 'Data panen berhasil dicatat!');
    }

    /* ─────────────────────────────────────────
     | Helper: Hitung Health Score
     ───────────────────────────────────────── */
    private function hitungHealth($sensor): int
    {
        if (!$sensor) return 60;

        $skor = 100;
        if ($sensor->tds < 800)      $skor -= 20;
        elseif ($sensor->tds > 1400) $skor -= 10;
        if ($sensor->ph < 5.5)       $skor -= 20;
        elseif ($sensor->ph > 6.5)   $skor -= 10;
        if ($sensor->suhu < 18)      $skor -= 10;
        elseif ($sensor->suhu > 25)  $skor -= 10;

        return max(0, $skor);
    }
}