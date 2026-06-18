<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id('id_notif');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_analisis')->constrained('analisis_ai', 'id_analisis')->onDelete('cascade');
            $table->text('pesan');
            $table->boolean('status_baca')->default(false);
            $table->timestamp('waktu_kirim');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
