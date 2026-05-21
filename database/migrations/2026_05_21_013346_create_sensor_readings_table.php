<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->string('perangkat');        // 'Perangkat A', 'B', dst
            $table->float('tds');                // ppm
            $table->float('ph', 3, 1);
            $table->float('suhu', 4, 1);              // °C
            $table->float('kelembapan');        // %
            $table->timestamp('dibaca_pada');   // waktu baca sensor
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};