<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analisis_ai', function (Blueprint $table) {
            $table->id('id_analisis');
            $table->foreignId('id_sensor')->constrained('data_sensor', 'id_sensor')->onDelete('cascade');
            $table->string('kondisi_nutrisi');   // 'optimal', 'deficiency', 'warning'
            $table->text('rekomendasi');         // hasil rekomendasi dari AI
            $table->timestamp('waktu_analisis');
            $table->enum('status_tindakan', ['belum', 'diterapkan', 'selesai'])->default('belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analisis_ai');
    }
};
