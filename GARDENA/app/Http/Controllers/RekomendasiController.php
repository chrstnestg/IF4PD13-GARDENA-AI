<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\AnalisisAi;
use App\Models\RiwayatPanen;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RekomendasiController extends Controller
{
    public function index()
    {
        $sensor = DataSensor::orderBy('id_sensor', 'desc')->first();

        $userId = Auth::id();
        $sedangCooldown = Cache::has('rekomendasi_cooldown_' . $userId);
        $sisaDetikCooldown = 0;

        if ($sedangCooldown) {
            $waktuSelesaiCooldown = Cache::get('rekomendasi_cooldown_expires_at_' . $userId);
            if ($waktuSelesaiCooldown) {
                $sisaDetikCooldown = max(0, now()->diffInSeconds($waktuSelesaiCooldown, false));
            }
        }

        $analisis = AnalisisAi::with('dataSensor')
            ->where('status_tindakan', 'belum')
            ->latest('waktu_analisis')
            ->first();

        $healthScore = 100;
        $healthLabel = 'Optimal';
        $kondisiAktif = null;
        $aiBermasalah = false; // flag baru untuk Blade

        if ($analisis) {
            $dataGemini = json_decode($analisis->rekomendasi, true) ?? [];

            // ── Cek dulu apakah ini record kegagalan AI, bukan hasil analisis normal ──
            if (!empty($dataGemini['error'])) {
                $aiBermasalah = true;

                $kondisiAktif = [
                    'id'          => $analisis->id_analisis,
                    'errorType'   => $dataGemini['error_type'] ?? 'unknown',
                    'errorMessage' => $dataGemini['message'] ?? 'Analisis AI sedang tidak tersedia. Coba lagi beberapa saat.',
                    'nilaiSaatIni' => $sensor ? "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C" : '-',
                    'nilaiOptimal' => 'TDS: 400–1200 ppm | pH: 6.0–8.0 | Suhu: 20–28°C',
                    'kritis'      => false, // jangan tampilkan sebagai "kondisi kritis tanaman", ini masalah sistem
                ];

                // Health score dibiarkan netral karena kita memang tidak tahu kondisi sebenarnya
                $healthScore = null;
                $healthLabel = 'Tidak Diketahui';

            } else {
                // ── Alur normal (sudah ada sebelumnya) ──
                $risk = $dataGemini['risk'] ?? 'low';

                if ($risk === 'high') {
                    $labelStatus = 'Kritis';
                    $bgLabelClass = 'bg-red-100 text-red-600';
                    $healthScore = 35;
                    $healthLabel = 'Buruk';
                } elseif ($risk === 'medium') {
                    $labelStatus = 'Peringatan';
                    $bgLabelClass = 'bg-orange-100 text-orange-600';
                    $healthScore = 65;
                    $healthLabel = 'Sedang';
                } else {
                    $labelStatus = 'Optimal';
                    $bgLabelClass = 'bg-green-100 text-green-600';
                    $healthScore = 100;
                    $healthLabel = 'Optimal';
                }

                $kondisiAktif = [
                    'id'           => $analisis->id_analisis,
                    'summary'      => $dataGemini['summary'] ?? 'Tidak ada ringkasan.',
                    'trend'        => $dataGemini['trend_analysis'] ?? '',
                    'pattern'      => $dataGemini['pattern_analysis'] ?? '',
                    'labelStatus'  => $labelStatus,
                    'bgLabelClass' => $bgLabelClass,
                    'nilaiSaatIni' => $sensor ? "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C" : '-',
                    'nilaiOptimal' => 'TDS: 400–1200 ppm | pH: 6.0–8.0 | Suhu: 20–28°C',
                    'aksiList'     => $dataGemini['recommendation'] ?? [],
                    'kritis'       => ($risk === 'high' || $risk === 'medium'),
                ];
            }
        }

        if ($sedangCooldown) {
            $healthScore = 100;
            $healthLabel = 'Stabilisasi';
        }

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
            'kondisiAktif',
            'healthScore',
            'healthLabel',
            'chartLabels',
            'chartTds',
            'chartPh',
            'chartSuhu',
            'sedangCooldown',
            'sisaDetikCooldown',
            'aiBermasalah'
        ));
    }

    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required']);

        $analisis = AnalisisAi::with('dataSensor')->find($request->nutrisi_id);

        if ($analisis) {
            $analisis->update(['status_tindakan' => 'selesai']);

            RiwayatPanen::create([
                'id_user'          => Auth::id(),
                'status_anomali'   => 'Anomali Teratasi',
                'rekomendasi_ai'   => Str::limit($analisis->kondisi_nutrisi, 250),
                'status_perbaikan' => 'Teratasi',
                'nilai_ph'         => $analisis->dataSensor ? $analisis->dataSensor->ph : null,
                'nilai_tds'        => $analisis->dataSensor ? $analisis->dataSensor->ec_tds : null,
                'nilai_suhu'       => $analisis->dataSensor ? $analisis->dataSensor->suhu : null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $userId = Auth::id();
        $waktuHabis = now()->addMinutes(5);
        Cache::put('rekomendasi_cooldown_' . $userId, true, $waktuHabis);
        Cache::put('rekomendasi_cooldown_expires_at_' . $userId, $waktuHabis, $waktuHabis);

        return redirect()
            ->route('rekomendasi')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Tindakan Berhasil!',
                'text'  => 'Sistem memasuki masa jeda sirkulasi selama 5 menit.',
            ]);
    }
}