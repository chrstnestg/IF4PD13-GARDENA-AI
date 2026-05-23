<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_device')
                  ->nullable()
                  ->constrained('perangkat_iot', 'id_device')
                  ->nullOnDelete();
            $table->integer('siklus');
            $table->date('tanggal_panen');
            $table->decimal('berat_panen', 6, 2);
            $table->integer('jumlah_ikat');
            $table->string('kualitas', 2);
            $table->integer('avg_health');
            $table->float('avg_tds')->nullable();
            $table->float('avg_ph')->nullable();
            $table->float('avg_suhu')->nullable();
            $table->float('avg_kelembapan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_panen');
    }
};