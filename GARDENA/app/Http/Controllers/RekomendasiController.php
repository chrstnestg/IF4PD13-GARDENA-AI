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

        // ── Auto generate analisis kalau sensor ada tapi analisis kosong ──
        if ($sensor) {
            $analisisAda = AnalisisAi::where('status_tindakan', '!=', 'selesai')
                ->latest('waktu_analisis')
                ->first();

            if (!$analisisAda) {
                $kondisiNutrisi = $this->tentukanKondisi($sensor);
                $rekomendasi    = $this->rekomendasiDariKondisi($kondisiNutrisi);

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
            $kondisiAktif = [
                'id'          => $analisis->id_analisis,
                'judul'       => $this->judulDariKondisi($analisis->kondisi_nutrisi),
                'status'      => $analisis->kondisi_nutrisi,
                'labelStatus' => $this->labelDariKondisi($analisis->kondisi_nutrisi),
                'nilaiSaatIni'=> $this->nilaiSensor($sensor),
                'nilaiOptimal'=> $this->nilaiOptimal(),
                'deskripsi'   => $this->deskripsiDariKondisi($analisis->kondisi_nutrisi),
                'aksiList'    => json_decode($analisis->rekomendasi, true) ?? [],
                'kritis'      => in_array($analisis->kondisi_nutrisi, ['pH Rendah', 'pH Tinggi', 'Nutrisi Berlebih']),
                'pesanKritis' => $this->pesanKritisDariKondisi($analisis->kondisi_nutrisi),
                'isNormal'    => $analisis->kondisi_nutrisi === 'Normal',
            ];
        }

        // ── Chart 7 hari terakhir ──
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
            'chartLabels', 'chartTds', 'chartPh', 'chartSuhu',
        ));
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

    private function tentukanKondisi(DataSensor $sensor): string
    {
        if ($sensor->ph < 5.5)                              return 'pH Rendah';
        if ($sensor->ph > 6.5)                              return 'pH Tinggi';
        if ($sensor->ec_tds < 800)                          return 'Nutrisi Kurang';
        if ($sensor->ec_tds > 1400)                         return 'Nutrisi Berlebih';
        if ($sensor->suhu < 18 || $sensor->suhu > 25)       return 'Suhu Tidak Ideal';

        return 'Normal';
    }

    private function rekomendasiDariKondisi(string $kondisi): array
    {
        return match($kondisi) {
            'pH Rendah'        => [
                'Tambahkan larutan pH Up (Kalium Hidroksida) secara bertahap',
                'Aduk larutan dan tunggu 10 menit sebelum mengukur ulang',
                'Target pH antara 5.5 - 6.5',
            ],
            'pH Tinggi'        => [
                'Tambahkan larutan pH Down (Asam Fosfat) secara bertahap',
                'Aduk larutan dan tunggu 10 menit sebelum mengukur ulang',
                'Target pH antara 5.5 - 6.5',
            ],
            'Nutrisi Kurang'   => [
                'Tambahkan nutrisi AB Mix sesuai dosis anjuran',
                'Ukur ulang TDS setelah 15 menit',
                'Target TDS antara 800 - 1400 ppm',
            ],
            'Nutrisi Berlebih' => [
                'Encerkan larutan dengan menambahkan air bersih',
                'Buang sebagian larutan lama jika perlu',
                'Target TDS antara 800 - 1400 ppm',
            ],
            'Suhu Tidak Ideal' => [
                'Periksa sirkulasi udara di sekitar instalasi hidroponik',
                'Gunakan coolant atau pemanas air jika diperlukan',
                'Target suhu antara 18°C - 25°C',
            ],
            default            => [
                'Pertahankan kondisi nutrisi yang sudah optimal',
                'Lakukan pengecekan rutin setiap hari',
                'Catat perkembangan tanaman secara berkala',
            ],
        };
    }

    private function hitungHealthScore(?DataSensor $sensor): array
    {
        if (!$sensor) return [0, 'Tidak Ada Data'];

        $skor = 100;

        // pH
        if ($sensor->ph < 5.0)          $skor -= 40;
        elseif ($sensor->ph < 5.5)      $skor -= 25;
        elseif ($sensor->ph > 7.0)      $skor -= 30;
        elseif ($sensor->ph > 6.5)      $skor -= 15;

        // TDS
        if ($sensor->ec_tds < 800)      $skor -= 25;
        elseif ($sensor->ec_tds < 1000) $skor -= 15;
        elseif ($sensor->ec_tds > 1800) $skor -= 20;
        elseif ($sensor->ec_tds > 1500) $skor -= 10;

        // Suhu
        if ($sensor->suhu < 18 || $sensor->suhu > 25) $skor -= 15;

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
            'Nutrisi Kurang'   => 'Nutrisi / TDS Kurang',
            'Nutrisi Berlebih' => 'Nutrisi / TDS Berlebih',
            'pH Rendah'        => 'pH Terlalu Rendah',
            'pH Tinggi'        => 'pH Terlalu Tinggi',
            'Suhu Tidak Ideal' => 'Suhu Tidak Ideal',
            default            => 'Kondisi Normal',
        };
    }

    private function labelDariKondisi(string $kondisi): string
    {
        return match($kondisi) {
            'Nutrisi Kurang', 'Suhu Tidak Ideal' => 'Perlu Perhatian',
            'Nutrisi Berlebih', 'pH Tinggi'      => 'Peringatan',
            'pH Rendah'                          => 'Kritis',
            default                              => 'Optimal',
        };
    }

    private function deskripsiDariKondisi(string $kondisi): string
    {
        return match($kondisi) {
            'Nutrisi Kurang'   => 'Kadar TDS di bawah batas minimum. Tanaman kekurangan unsur hara.',
            'Nutrisi Berlebih' => 'Kadar TDS melebihi batas maksimum. Dapat merusak akar tanaman.',
            'pH Rendah'        => 'Tingkat keasaman terlalu tinggi. Penyerapan nutrisi terhambat.',
            'pH Tinggi'        => 'Larutan terlalu basa. Nutrisi tidak dapat diserap optimal.',
            'Suhu Tidak Ideal' => 'Suhu air di luar rentang ideal. Pertumbuhan tanaman terganggu.',
            default            => 'Semua parameter dalam kondisi optimal.',
        };
    }

    private function pesanKritisDariKondisi(string $kondisi): ?string
    {
        return match($kondisi) {
            'pH Rendah'        => 'Segera tangani! pH rendah dapat mematikan tanaman dalam waktu singkat.',
            'pH Tinggi'        => 'Segera tangani! pH tinggi menghambat seluruh penyerapan nutrisi.',
            'Nutrisi Berlebih' => 'Waspadai! Kelebihan nutrisi dapat membakar akar tanaman.',
            default            => null,
        };
    }

    private function nilaiSensor(?DataSensor $sensor): string
    {
        if (!$sensor) return '-';
        return "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C";
    }

    private function nilaiOptimal(): string
    {
        return 'TDS: 800–1400 ppm | pH: 5.5–6.5 | Suhu: 18–25°C';
    }
}