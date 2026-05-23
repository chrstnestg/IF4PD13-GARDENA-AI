<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\AnalisisAi;

class RekomendasiController extends Controller
{
    public function index()
    {
        // ── Ambil sensor terbaru ──
        $sensor = DataSensor::latest('dibaca_pada')->first();

        // ── Health score dari sensor ──
        [$healthScore, $healthLabel] = $this->hitungHealthScore($sensor);

        // ── Ambil rekomendasi dari analisis AI, filter yang sudah selesai ──
        $rekomendasiList = AnalisisAi::with('dataSensor')
            ->where('status_tindakan', '!=', 'selesai')
            ->latest('waktu_analisis')
            ->get()
            ->map(fn($a) => [
                'id'          => $a->id_analisis,
                'judul'       => $this->judulDariKondisi($a->kondisi_nutrisi),
                'status'      => $a->kondisi_nutrisi,
                'labelStatus' => $this->labelDariKondisi($a->kondisi_nutrisi),
                'nilaiSaatIni'=> $this->nilaiSensor($a->dataSensor),
                'nilaiOptimal'=> $this->nilaiOptimal($a->kondisi_nutrisi),
                'deskripsi'   => '',
                'aksiList'    => json_decode($a->rekomendasi, true) ?? [],
                'kritis'      => false,
                'pesanKritis' => null,
            ])->toArray();

        // ── Chart 7 hari terakhir ──
        $chartLabels = $chartTds = $chartPh = $chartSuhu = $chartKelembapan = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal           = now()->subDays($i);
            $rata              = DataSensor::whereDate('dibaca_pada', $tanggal)->get();
            $chartLabels[]     = $tanggal->translatedFormat('j M');
            $chartTds[]        = $rata->isNotEmpty() ? round($rata->avg('ec_tds'), 2)      : 0;
            $chartPh[]         = $rata->isNotEmpty() ? round($rata->avg('ph'), 2)           : 0;
            $chartSuhu[]       = $rata->isNotEmpty() ? round($rata->avg('suhu'), 2)         : 0;
            $chartKelembapan[] = $rata->isNotEmpty() ? round($rata->avg('kelembapan'), 2)   : 0;
        }

        return view('pages.rekomendasi', compact(
            'rekomendasiList', 'healthScore', 'healthLabel',
            'chartLabels', 'chartTds', 'chartPh', 'chartSuhu', 'chartKelembapan',
        ));
    }

    public function terapkan(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required']);

        AnalisisAi::where('id_analisis', $request->nutrisi_id)
            ->update(['status_tindakan' => 'diterapkan']);

        return redirect()->route('rekomendasi')
            ->with('success', 'Rekomendasi berhasil diterapkan!');
    }

    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required']);

        AnalisisAi::where('id_analisis', $request->nutrisi_id)
            ->update(['status_tindakan' => 'selesai']);

        return redirect()->route('rekomendasi')
            ->with('success', 'Tindakan dicatat sebagai sudah dilakukan.');
    }

    // ══════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════

    private function hitungHealthScore(?DataSensor $sensor): array
    {
        if (!$sensor) return [0, 'Tidak Ada Data'];

        $skor = 100;

        if ($sensor->ec_tds < 800)        $skor -= 20;
        elseif ($sensor->ec_tds > 1400)   $skor -= 10;

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

    private function judulDariKondisi(string $kondisi): string
    {
        return match($kondisi) {
            'deficiency' => 'Nutrisi / TDS',
            'warning'    => 'Peringatan Sensor',
            default      => 'Kondisi Normal',
        };
    }

    private function labelDariKondisi(string $kondisi): string
    {
        return match($kondisi) {
            'deficiency' => 'Kekurangan',
            'warning'    => 'Peringatan',
            default      => 'Optimal',
        };
    }

    private function nilaiSensor(?DataSensor $sensor): string
    {
        if (!$sensor) return '-';
        return "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C";
    }

    private function nilaiOptimal(string $kondisi): string
    {
        return 'TDS: 800–1400 ppm | pH: 5.5–6.5 | Suhu: 18–25°C';
    }
}