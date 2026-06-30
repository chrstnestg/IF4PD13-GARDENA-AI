<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Models\DataSensor;

Route::post('/sensor', [SensorController::class, 'store']);

Route::get('/latest-sensor', function () {

    return \App\Models\DataSensor::orderBy('id_sensor', 'desc')->first();

});