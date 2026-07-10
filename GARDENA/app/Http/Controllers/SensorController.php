<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use Illuminate\Support\Facades\Http;

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

            // 2. Simpan data sensor terbaru ke MySQL (tanpa id_device sesuai update skema baru)
            $data = DataSensor::create([
                'ph'           => $request->ph,
                'suhu'         => $request->suhu,
                'ec_tds'       => $request->ec_tds,
                'status_valid' => true,
                'dibaca_pada'  => now(),
            ]);

            // 3. Tarik 30 rekaman data sensor terakhir sebagai dataset historis untuk dikirim ke Python
            $historis = DataSensor::orderBy('dibaca_pada', 'desc')
                ->take(30)
                ->get()
                ->map(function ($item) {
                    return [
                        'ph'   => (float)$item->ph,
                        'suhu' => (float)$item->suhu,
                        'ec_tds'  => (float)$item->ec_tds, // Disamakan dengan key 'tds' di FastAPI
                    ];
                })
                ->reverse() // Urutkan secara kronologis (dari waktu terlama ke terbaru)
                ->values();

            // 4. Kirim data ke FastAPI (Sesuaikan portnya dengan port FastAPI-mu berjalan, misal: 8001)
            $response = Http::timeout(90)->post('http://127.0.0.1:8001/historical-insight', [
                'id_sensor' => $data->id,
                'history'   => $historis
            ]);

            // 5. Simpan Hasil Analisis AI yang berupa JSON Teks Terstruktur ke dalam tabel analisis_ai
            if ($response->successful()) {
                $hasilAi = $response->json();
    
                // Jangan di-implode, biarkan data utuh dari FastAPI disimpan sebagai JSON string
                $jsonUtuhGemini = json_encode($hasilAi);

                \DB::table('analisis_ai')->insert([
                    'id_sensor'       => $data->id,
                    'kondisi_nutrisi' => $hasilAi['summary'], // Tetap simpan ringkasan teks singkat di sini
                    'rekomendasi'     => $jsonUtuhGemini,     // <--- SIMPAN SELURUH JSON UTUH DI SINI
                    'waktu_analisis'  => now(),
                    'status_tindakan' => (($hasilAi['risk'] ?? 'low') === 'low') ? 'selesai' : 'belum',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                }

            return response()->json([
                'message'     => 'Berhasil menyimpan data sensor dan memperbarui Analisis AI',
                'sensor_data' => $data,
                'ai_status' => $response->successful() 
                    ? 'Success Terintegrasi' 
                    : 'Gagal (' . $response->status() . '): ' . $response->body()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}