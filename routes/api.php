<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SensorController;
use App\Models\DataSensor;

Route::post('/sensor', [SensorController::class, 'store']);

Route::get('/latest-sensor', function () {

    return DataSensor::latest('dibaca_pada')->first();

});