<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\DataSensor;
use App\Models\AnalisisAi;

class PrediksiService
{
    protected string $fastapiUrl = 'http://127.0.0.1:8001';

    public function analisisAndSave(int $idSensor): void
    {
        // Ambil 20 data sensor terbaru (urut dari lama ke baru)
        $readings = DataSensor::latest('dibaca_pada')
            ->take(20)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($r) => [
                (float) $r->ph,
                (float) $r->ec_tds,
                (float) $r->suhu,
            ])
            ->toArray();

        // Minimal harus ada 20 data
        if (count($readings) < 20) return;

        // Panggil FastAPI
        $response = Http::timeout(10)->post("{$this->fastapiUrl}/predict", [
            'data' => $readings
        ]);

        if ($response->failed()) return;

        $hasil       = $response->json();
        $kondisiBaru = $hasil['kondisi'];

        // Hanya simpan kalau kondisi BERUBAH
        $analisisTerakhir = AnalisisAi::latest('waktu_analisis')->first();
        if ($analisisTerakhir && $analisisTerakhir->kondisi_nutrisi === $kondisiBaru) return;

        AnalisisAi::create([
            'id_sensor'       => $idSensor,
            'kondisi_nutrisi' => $kondisiBaru,
            'rekomendasi'     => json_encode($hasil['rekomendasi']),
            'waktu_analisis'  => now(),
            'status_tindakan' => 'belum',
        ]);
    }
}