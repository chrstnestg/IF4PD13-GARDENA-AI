<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;
use Carbon\Carbon;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {
            // 1. Validasi nilai negatif dari ESP32
            if ($request->ph < 0 || $request->suhu < 0 || $request->ec_tds < 0) {
                return response()->json([
                    'error' => 'Data tidak valid: nilai sensor tidak boleh negatif'
                ], 422);
            }

            // 2. Simpan data sensor terbaru ke MySQL
            $data = DataSensor::create([
                'ph'           => $request->ph,
                'suhu'         => $request->suhu,
                'ec_tds'       => $request->ec_tds,
                'status_valid' => true,
                'dibaca_pada'  => now(),
            ]);

            // 3. Tarik 30 rekaman data sensor terakhir sebagai dataset historis untuk dikirim ke Python
            //    Sertakan timestamp -> dipakai FastAPI untuk hitung durasi tren yang sebenarnya,
            //    bukan asumsi "1 pembacaan = 1 menit".
            $historis = DataSensor::orderBy('dibaca_pada', 'desc')
                ->take(30)
                ->get()
                ->map(function ($item) {
                    return [
                        'ph'        => (float) $item->ph,
                        'suhu'      => (float) $item->suhu,
                        'ec_tds'    => (float) $item->ec_tds,
                        'timestamp' => Carbon::parse($item->dibaca_pada)->toIso8601String(),
                    ];
                })
                ->reverse() // Urutkan kronologis: terlama -> terbaru
                ->values();

            // 4. Kirim ke FastAPI, dengan penanganan gagal koneksi terpisah dari gagal status HTTP
            $response = null;
            $aiStatusMessage = null;

            try {
                $response = Http::timeout(90)->post('http://127.0.0.1:8001/historical-insight', [
                    'id_sensor' => $data->id,
                    'history'   => $historis,
                ]);
            } catch (ConnectionException $e) {
                // FastAPI mati / tidak bisa dijangkau sama sekali
                Log::warning('FastAPI tidak dapat dijangkau: ' . $e->getMessage());
                $this->simpanAnalisisGagal($data->id, 'connection', 'Layanan analisis AI sedang tidak dapat dijangkau.');
                return response()->json([
                    'message'     => 'Data sensor tersimpan, tetapi analisis AI gagal (koneksi ke server AI terputus).',
                    'sensor_data' => $data,
                    'ai_status'   => 'Gagal: Tidak dapat menjangkau layanan AI',
                ], 200);
            }

            // 5. Simpan hasil sesuai status response FastAPI
            if ($response->successful()) {
                $hasilAi = $response->json();
                $jsonUtuhGemini = json_encode($hasilAi);

                \DB::table('analisis_ai')->insert([
                    'id_sensor'       => $data->id,
                    'kondisi_nutrisi' => $hasilAi['summary'] ?? 'Tidak ada ringkasan.',
                    'rekomendasi'     => $jsonUtuhGemini,
                    'waktu_analisis'  => now(),
                    'status_tindakan' => (($hasilAi['risk'] ?? 'low') === 'low') ? 'selesai' : 'belum',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                $aiStatusMessage = 'Success Terintegrasi';
            } elseif ($response->status() === 429) {
                // Quota Gemini habis -> sudah ditangani main.py dengan HTTPException(429),
                // di sini tinggal disimpan sebagai state yang jujur ke user, bukan didiamkan.
                Log::warning('Gemini rate limited (429) saat memproses id_sensor ' . $data->id);
                $this->simpanAnalisisGagal(
                    $data->id,
                    'rate_limit',
                    'Kuota layanan AI (Gemini) sedang habis. Analisis akan tersedia kembali setelah kuota direset.'
                );
                $aiStatusMessage = 'Gagal (429): Kuota AI habis';
            } else {
                // Error lain dari FastAPI (500, validasi, dsb)
                Log::warning('FastAPI mengembalikan status ' . $response->status() . ': ' . $response->body());
                $this->simpanAnalisisGagal(
                    $data->id,
                    'server_error',
                    'Analisis AI gagal diproses. Sistem akan mencoba lagi pada pembacaan berikutnya.'
                );
                $aiStatusMessage = 'Gagal (' . $response->status() . '): ' . $response->body();
            }

            return response()->json([
                'message'     => 'Berhasil menyimpan data sensor.',
                'sensor_data' => $data,
                'ai_status'   => $aiStatusMessage,
            ], 200);

        } catch (\Exception $e) {
            Log::error('SensorController@store error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan penanda kegagalan analisis AI ke tabel analisis_ai,
     * supaya halaman /rekomendasi bisa menampilkan state jujur
     * ("AI sedang bermasalah") alih-alih diam-diam dianggap "Optimal".
     */
    private function simpanAnalisisGagal(int $idSensor, string $tipeError, string $pesan): void
    {
        $payloadError = json_encode([
            'error'      => true,
            'error_type' => $tipeError, // 'rate_limit' | 'server_error' | 'connection'
            'message'    => $pesan,
        ]);

        \DB::table('analisis_ai')->insert([
            'id_sensor'       => $idSensor,
            'kondisi_nutrisi' => 'AI_ERROR',
            'rekomendasi'     => $payloadError,
            'waktu_analisis'  => now(),
            // status_tindakan 'belum' -> tetap muncul di dashboard sampai ada pembacaan sukses berikutnya
            'status_tindakan' => 'belum',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}