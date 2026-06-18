<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perangkat_iot', function (Blueprint $table) {
            $table->id('id_device');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('nama_device');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('perangkat_iot');
    }
};
