<?php

namespace App\Http\Controllers;

use App\Models\DataSensor;

class MonitoringController extends Controller
{
    public function index()
    {
        // data terbaru
        $sensor = DataSensor::latest('dibaca_pada')->first();

        // history chart
        $history = DataSensor::latest('dibaca_pada')
            ->take(10)
            ->get()
            ->reverse();

        return view('pages.monitoring', compact(
            'sensor',
            'history'
        ));
    }
}