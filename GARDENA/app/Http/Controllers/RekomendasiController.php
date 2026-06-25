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

    // ── Selalu generate analisis baru berdasarkan sensor terbaru ──
    if ($sensor) {
        $kondisiNutrisi = $this->tentukanKondisi($sensor);

        $analisisAda = AnalisisAi::where('status_tindakan', '!=', 'selesai')
            ->where('kondisi_nutrisi', $kondisiNutrisi)  // ← cek kondisi sama
            ->where('id_sensor', $sensor->id_sensor)     // ← dari sensor yg sama
            ->latest('waktu_analisis')
            ->first();

        if (!$analisisAda) {
            $rekomendasi = $this->rekomendasiDariKondisi($kondisiNutrisi);

            AnalisisAi::create([
                'id_sensor'       => $sensor->id_sensor,
                'kondisi_nutrisi' => $kondisiNutrisi,
                'rekomendasi'     => json_encode($rekomendasi),
                'waktu_analisis'  => now(),
                'status_tindakan' => 'belum',
            ]);
        }
    }

    // ── Health score dari sensor terbaru ──
    [$healthScore, $healthLabel] = $this->hitungHealthScore($sensor);

    // ── Ambil 1 kondisi aktif terbaru ──
    $analisis = AnalisisAi::with('dataSensor')
        ->where('status_tindakan', '!=', 'selesai')
        ->latest('waktu_analisis')
        ->first();

        $kondisiAktif = null;
        if ($analisis) {
            $kondisi = $analisis->kondisi_nutrisi;
            $isNormal = ($kondisi === 'Normal');

            $kondisiAktif = [
                'id'           => $analisis->id_analisis,
                'judul'        => $isNormal ? 'Kondisi Normal' : 'Terdeteksi Masalah: ' . $kondisi,
                'status'       => $kondisi,
                'labelStatus'  => $isNormal ? 'Optimal' : (str_contains($kondisi, 'pH Rendah') ? 'Kritis' : 'Peringatan'),
                'nilaiSaatIni' => $sensor ? "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C" : '-',
                'nilaiOptimal' => 'TDS: 800–1400 ppm | pH: 5.5–6.5 | Suhu: 18–25°C',
                'deskripsi'    => $isNormal ? 'Semua parameter dalam kondisi optimal.' : 'Sistem mendeteksi adanya ketidaksesuaian parameter pada larutan nutrisi sawi putih.',
                'aksiList'     => json_decode($analisis->rekomendasi, true) ?? [], // LANGSUNG BACA 3 REKOMENDASI DARI PYTHON
                'kritis'       => !$isNormal,
                'pesanKritis'  => $isNormal ? null : 'Segera lakukan tindakan penanganan sesuai instruksi AI di bawah ini.',
                'isNormal'     => $isNormal,
            ];
        }

        // 4. Data untuk Chart 7 Hari (Dipertahankan)
        $chartLabels = $chartTds = $chartPh = $chartSuhu = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal       = now()->subDays($i);
            $rata          = DataSensor::whereDate('dibaca_pada', $tanggal)->get();
            $chartLabels[] = $tanggal->translatedFormat('j M');
            $chartTds[]    = $rata->isNotEmpty() ? round($rata->avg('ec_tds'), 2) : 0;
            $chartPh[]     = $rata->isNotEmpty() ? round($rata->avg('ph'), 2)     : 0;
            $chartSuhu[]   = $rata->isNotEmpty() ? round($rata->avg('suhu'), 2)   : 0;
        }

        return view('pages.rekomendasi', compact(
            'kondisiAktif', 'healthScore', 'healthLabel',
            'chartLabels', 'chartTds', 'chartPh', 'chartSuhu'
        ));
    }

    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required']);
        AnalisisAi::where('id_analisis', $request->nutrisi_id)->update(['status_tindakan' => 'selesai']);

        return redirect()->route('rekomendasi')->with('success', 'Tindakan dicatat sebagai sudah dilakukan.');
    }

    private function hitungHealthScore(?DataSensor $sensor): array
    {
        if (!$sensor) return [0, 'Tidak Ada Data'];
        $skor = 100;

        if ($sensor->ph < 5.0) $skor -= 40;
        elseif ($sensor->ph < 5.5) $skor -= 25;
        if ($sensor->ph > 7.0) $skor -= 30;
        elseif ($sensor->ph > 6.5) $skor -= 15;

        if ($sensor->ec_tds < 800) $skor -= 25;
        elseif ($sensor->ec_tds < 1000) $skor -= 15;
        if ($sensor->ec_tds > 1800) $skor -= 20;
        elseif ($sensor->ec_tds > 1500) $skor -= 10;

        if ($sensor->suhu < 18 || $sensor->suhu > 25) $skor -= 15;

        $skor = max(0, $skor);
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