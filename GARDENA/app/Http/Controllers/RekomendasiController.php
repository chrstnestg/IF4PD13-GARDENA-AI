<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\AnalisisAi;
use App\Models\RiwayatPanen; // Pastikan model riwayat ini di-import
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RekomendasiController extends Controller
{
    /**
     * Tampilan Utama Halaman Rekomendasi
     */
    public function index()
    {
        $sensor = DataSensor::orderBy('id_sensor', 'desc')->first();

        $healthScore = 0;
        $healthLabel = 'Tidak Ada Data';
        $sisaDetikCooldown = 0;

        // 1. CEK STATUS COOLDOWN VIA CACHE SERVER (Spesifik per User)
        $userId = Auth::id();
        $sedangCooldown = Cache::has('rekomendasi_cooldown_' . $userId);

        if ($sedangCooldown) {
            // Hitung sisa waktu cooldown dalam detik khusus user ini agar bisa ditampilkan di UI
            $waktuSelesaiCooldown = Cache::get('rekomendasi_cooldown_expires_at_' . $userId);
            if ($waktuSelesaiCooldown) {
                $sisaDetikCooldown = max(0, now()->diffInSeconds($waktuSelesaiCooldown, false));
            }
        }

        // Sistem HANYA menembak API Python jika TIDAK sedang dalam masa cooldown
        if ($sensor && !$sedangCooldown) {
            try {
                $response = Http::timeout(5)->post('http://127.0.0.1:8001/predict', [
                    'ph'   => (float) $sensor->ph,
                    'tds'  => (float) $sensor->ec_tds,
                    'suhu' => (float) $sensor->suhu,
                ]);

                if ($response->successful()) {
                    $hasilAi = $response->json();

                    $healthScore = $hasilAi['health_score'];
                    $healthLabel = $hasilAi['status_tanaman'];

                    // Cek analisis aktif berstatus 'belum'
                    $analisisAktif = AnalisisAi::where('status_tindakan', 'belum')
                        ->latest('waktu_analisis')
                        ->first();

                    // Buat analisis baru jika belum ada yang aktif
                    if (!$analisisAktif) {
                        AnalisisAi::create([
                            'id_sensor'       => $sensor->id_sensor,
                            'kondisi_nutrisi' => $hasilAi['kondisi'],
                            'rekomendasi'     => json_encode($hasilAi['rekomendasi']),
                            'waktu_analisis'  => now(),
                            'status_tindakan' => $hasilAi['kondisi'] === 'Normal' ? 'selesai' : 'belum',
                        ]);
                    }
                }
            } catch (\Exception $e) {
                report($e);
                $healthScore = 50;
                $healthLabel = 'API Python Offline';
            }
        }

        // 2. KONDISI TAMPILAN DATA (JIKA COOLDOWN VS TIDAK COOLDOWN)
        if ($sedangCooldown) {
            $analisis = null;
            if ($sensor) {
                $healthScore = 100; 
                $healthLabel = 'Stabil';
            }
        } else {
            $analisis = AnalisisAi::with('dataSensor')
                ->where('status_tindakan', 'belum')
                ->latest('waktu_analisis')
                ->first();
        }

        $kondisiAktif = null;
        if ($analisis) {
            $kondisi         = $analisis->kondisi_nutrisi;
            $isNormal        = ($kondisi === 'Normal');
            $rekomendasiList = json_decode($analisis->rekomendasi, true) ?? [];

            $kondisiAktif = [
                'id'           => $analisis->id_analisis,
                'judul'        => $isNormal ? 'Kondisi Normal' : 'Terdeteksi Masalah: ' . $kondisi,
                'status'       => $kondisi,
                'labelStatus'  => $isNormal ? 'Optimal' : (str_contains($kondisi, 'pH') ? 'Kritis' : 'Peringatan'),
                'nilaiSaatIni' => $sensor ? "TDS: {$sensor->ec_tds} ppm | pH: {$sensor->ph} | Suhu: {$sensor->suhu}°C" : '-',
                'nilaiOptimal' => 'TDS: 400–1200 ppm | pH: 6.0–8.0 | Suhu: 20–28°C',
                'deskripsi'    => $isNormal ? 'Semua parameter dalam kondisi optimal.' : 'Sistem mendeteksi adanya ketidaksesuaian parameter pada larutan nutrisi sawi putih.',
                'aksiList'     => $rekomendasiList,
                'kritis'       => !$isNormal,
                'pesanKritis'  => $isNormal ? null : 'Segera lakukan tindakan penanganan sesuai instruksi AI di bawah ini.',
                'isNormal'     => $isNormal,
            ];
        }

        // Data Chart 7 Hari
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
            'sisaDetikCooldown'
        ));
    }

    /**
     * Aksi Saat Tombol "Sudah Ditangani" Ditekan
     */
    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required']);

        // 1. Cari data analisis yang aktif beserta relasi sensornya
        $analisis = AnalisisAi::with('dataSensor')->find($request->nutrisi_id);

        if ($analisis) {
            // Ubah status tindakan menjadi selesai di tabel analisis_ai
            $analisis->update(['status_tindakan' => 'selesai']);

            // 2. GABUNGKAN ARRAY REKOMENDASI MENJADI STRING
            $rekomendasiArray = json_decode($analisis->rekomendasi, true) ?? [];
            $rekomendasiString = !empty($rekomendasiArray) 
                ? implode(" | ", $rekomendasiArray) 
                : 'Tidak ada rekomendasi tindakan.';

            // 3. SELESAI & MASUKKAN LANGSUNG KE RIWAYAT ANOMALI (RiwayatPanen)
            RiwayatPanen::create([
                'id_user'          => Auth::id(), // ID User Petani yang login
                'status_anomali'   => $analisis->kondisi_nutrisi,
                'rekomendasi_ai'   => $rekomendasiString,
                'status_perbaikan' => 'Teratasi', // Status langsung diset Teratasi
                'nilai_ph'         => $analisis->dataSensor ? $analisis->dataSensor->ph : null,
                'nilai_tds'        => $analisis->dataSensor ? $analisis->dataSensor->ec_tds : null,
                'nilai_suhu'       => $analisis->dataSensor ? $analisis->dataSensor->suhu : null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // 4. SET LOCK TIMER COOLDOWN SELAMA 5 MENIT DI CACHE SERVER (Spesifik per User)
        $userId = Auth::id();
        $waktuHabis = now()->addMinutes(5);
        Cache::put('rekomendasi_cooldown_' . $userId, true, $waktuHabis);
        Cache::put('rekomendasi_cooldown_expires_at_' . $userId, $waktuHabis, $waktuHabis);

        return redirect()
            ->route('rekomendasi')
            ->with('swal', [
                'icon'  => 'success',
                'title' => 'Berhasil Dicatat!',
                'text'  => 'Tindakan tersimpan ke Riwayat. Sistem di-jeda selama 5 menit untuk sirkulasi air.',
            ]);
    }
}