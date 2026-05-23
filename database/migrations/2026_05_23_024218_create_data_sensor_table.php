<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_sensor', function (Blueprint $table) {
            $table->id('id_sensor');
            $table->foreignId('id_device')->constrained('perangkat_iot', 'id_device')->onDelete('cascade');
            $table->float('ph');
            $table->float('suhu');
            $table->integer('ec_tds');        // ppm
            $table->integer('kelembapan');    // %
            $table->boolean('status_valid')->default(true);
            $table->timestamp('dibaca_pada');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('data_sensor');
    }
};
