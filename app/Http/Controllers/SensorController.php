<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;

class SensorController extends Controller
{
    public function store(Request $request)
    {
        try {

            $data = DataSensor::create([

                'id_device'    => 1,

                'ph'           => $request->ph,

                'suhu'         => $request->suhu,

                'ec_tds'       => $request->tds,

                'status_valid' => true,

                'dibaca_pada'  => 

            ]);

            return response()->json([
                'message' => 'Berhasil',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}