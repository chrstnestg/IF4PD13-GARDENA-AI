<?php

namespace App\Http\Controllers;

use App\Models\DataSensor;

class MonitoringController extends Controller
{
    public function index()
    {
        // data terbaru
        $sensor = DataSensor::orderBy('id_sensor', 'desc')->first();

        // history chart
        $history = DataSensor::orderBy('id_sensor', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        return view('pages.monitoring', compact(
            'sensor',
            'history'
        ));
    }
}