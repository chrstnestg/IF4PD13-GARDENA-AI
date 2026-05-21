<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorReading;
use App\Models\Rekomendasi;
use App\Models\TindakanRekomendasi;

class RekomendasiController extends Controller
{
    public function index()
    {
        $sensor = SensorReading::latest('dibaca_pada')->first();

        // Health score dari sensor
        [$healthScore, $healthLabel] = $this->hitungHealthScore($sensor);

        // Filter rekomendasi yang sudah dilakukan
        $sudahDilakukan = TindakanRekomendasi::where('aksi', 'selesai')
            ->pluck('rekomendasi_id')
            ->toArray();

        $rekomendasiList = Rekomendasi::latest()
            ->whereNotIn('id', $sudahDilakukan)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->nutrisi_id,
                'judul'       => $r->judul,
                'status'      => $r->status,
                'labelStatus' => $r->label_status,
                'nilaiSaatIni'=> $r->nilai_saat_ini,
                'nilaiOptimal'=> $r->nilai_optimal,
                'deskripsi'   => $r->deskripsi,
                'aksiList'    => $r->aksi_list ?? [],
                'kritis'      => $r->kritis,
                'pesanKritis' => $r->pesan_kritis,
            ])->toArray();

        // Chart 7 hari terakhir
        $chartLabels = $chartTds = $chartPh = $chartSuhu = $chartKelembapan = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal           = now()->subDays($i);
            $rata              = SensorReading::whereDate('dibaca_pada', $tanggal)->get();
            $chartLabels[]     = $tanggal->translatedFormat('j M');
            $chartTds[]        = $rata->isNotEmpty() ? round($rata->avg('tds'), 2)        : 0;
            $chartPh[]         = $rata->isNotEmpty() ? round($rata->avg('ph'), 2)         : 0;
            $chartSuhu[]       = $rata->isNotEmpty() ? round($rata->avg('suhu'), 2)       : 0;
            $chartKelembapan[] = $rata->isNotEmpty() ? round($rata->avg('kelembapan'), 2) : 0;
        }

        return view('pages.rekomendasi', compact(
            'rekomendasiList', 'healthScore', 'healthLabel',
            'chartLabels', 'chartTds', 'chartPh', 'chartSuhu', 'chartKelembapan',
        ));
    }

    public function terapkan(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required|string']);

        $rek = Rekomendasi::where('nutrisi_id', $request->nutrisi_id)->first();
        if ($rek) {
            TindakanRekomendasi::create([
                'rekomendasi_id' => $rek->id,
                'aksi'           => 'terapkan',
                'dilakukan_pada' => now(),
            ]);
        }

        return redirect()->route('rekomendasi')
            ->with('success', 'Rekomendasi berhasil diterapkan!');
    }

    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required|string']);

        $rek = Rekomendasi::where('nutrisi_id', $request->nutrisi_id)->first();
        if ($rek) {
            TindakanRekomendasi::create([
                'rekomendasi_id' => $rek->id,
                'aksi'           => 'selesai',
                'dilakukan_pada' => now(),
            ]);
        }

        return redirect()->route('rekomendasi')
            ->with('success', 'Tindakan dicatat sebagai sudah dilakukan.');
    }

    private function hitungHealthScore(?SensorReading $sensor): array
    {
        if (!$sensor) return [0, 'Tidak Ada Data'];

        $skor = 100;

        if ($sensor->tds < 800)           $skor -= 20;
        elseif ($sensor->tds > 1400)      $skor -= 10;

        if ($sensor->ph < 5.5)            $skor -= 20;
        elseif ($sensor->ph > 6.5)        $skor -= 10;

        if ($sensor->suhu < 18)           $skor -= 10;
        elseif ($sensor->suhu > 25)       $skor -= 10;

        if ($sensor->kelembapan < 60)     $skor -= 5;
        elseif ($sensor->kelembapan > 80) $skor -= 5;

        $skor  = max(0, $skor);
        $label = match(true) {
            $skor >= 90 => 'Sangat Sehat',
            $skor >= 75 => 'Sehat',
            $skor >= 60 => 'Sedang',
            $skor >= 40 => 'Perlu Perhatian',
            default     => 'Kritis',
        };

        return [$skor, $label];
    }
}