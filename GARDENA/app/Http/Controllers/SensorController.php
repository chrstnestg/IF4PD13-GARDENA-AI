<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Services\PrediksiService;

class SensorController extends Controller
{
    public function store(Request $request, PrediksiService $prediksi)
    {
        try {
            $data = DataSensor::create([
                'id_device'    => 1,
                'ph'           => $request->ph,
                'suhu'         => $request->suhu,
                'ec_tds'       => $request->tds,
                'status_valid' => true,
                'dibaca_pada'  => now(),
            ]);

            // Panggil AI setiap data sensor masuk
            $prediksi->analisisAndSave($data->id_sensor);

            return response()->json([
                'message' => 'Berhasil',
                'data'    => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}