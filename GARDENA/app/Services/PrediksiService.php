<?php

namespace App\Services;

use App\Models\DataSensor;
use App\Models\AnalisisAi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrediksiService
{
    public function analisisAndSave($id_sensor)
    {
        $sensor = DataSensor::find($id_sensor);

        if (!$sensor) {
            return false;
        }

        try {
            // Tembak ke FastAPI main.py di port 8000
            $response = Http::timeout(5)->post('http://127.0.0.1:8001/predict', [
                'ph'   => (float) $sensor->ph,
                'tds'  => (float) $sensor->ec_tds, 
                'suhu' => (float) $sensor->suhu,
            ]);

            if ($response->successful()) {
                $hasilAi = $response->json();

                // Simpan hasil kondisi dan rekomendasi dari AI ke database
                AnalisisAi::create([
                    'id_sensor'       => $sensor->id_sensor,
                    'kondisi_nutrisi' => $hasilAi['kondisi'],
                    'rekomendasi'     => json_encode($hasilAi['rekomendasi']), 
                    'waktu_analisis'  => now(),
                    'status_tindakan' => 'belum',
                ]);

                return true;
            }

            Log::error('GARDENA AI Error: Python gagal merespon.');
            return false;

        } catch (\Exception $e) {
            Log::error('GARDENA AI Error: Gagal konek ke Python. ' . $e->getMessage());
            return false;
        }
    }
}