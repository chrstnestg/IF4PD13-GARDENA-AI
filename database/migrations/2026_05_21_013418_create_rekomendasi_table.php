<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rekomendasi', function (Blueprint $table) {
            $table->id();
            $table->string('nutrisi_id');       // 'ec', 'ph', 'suhu', 'kelembapan'
            $table->string('judul');
            $table->enum('status', ['optimal', 'deficiency', 'warning']);
            $table->string('label_status');
            $table->string('nilai_saat_ini');
            $table->string('nilai_optimal');
            $table->text('deskripsi')->nullable();
            $table->json('aksi_list');          // array aksi
            $table->boolean('kritis')->default(false);
            $table->text('pesan_kritis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekomendasi');
    }
};