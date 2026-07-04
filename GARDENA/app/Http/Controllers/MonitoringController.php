<?php

namespace App\Http\Controllers;

use App\Models\DataSensor;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        // Mengambil data terbaru dari database
        $sensor = DataSensor::orderBy('id_sensor', 'desc')->first();

        // Logika untuk mengecek apakah sensor aktif (data masuk dalam 5 menit terakhir)
        $sensorAktif = false;
        if ($sensor && $sensor->dibaca_pada) {
            $sensorAktif = Carbon::parse($sensor->dibaca_pada)->diffInMinutes(now()) < 5;
        }

        // Mengambil 10 data terakhir untuk kebutuhan Chart/Grafik
        $history = DataSensor::orderBy('id_sensor', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        return view('pages.monitoring', compact(
            'sensor',
            'history',
            'sensorAktif'
        ));
    }
}