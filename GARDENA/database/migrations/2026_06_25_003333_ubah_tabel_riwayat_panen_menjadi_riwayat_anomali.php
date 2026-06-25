<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ganti nama tabelnya terlebih dahulu
        Schema::rename('riwayat_panen', 'riwayat_anomali');

        // 2. Modifikasi kolom di dalam tabel yang baru (riwayat_anomali)
        Schema::table('riwayat_anomali', function (Blueprint $table) {
            // Hapus kolom lama yang sudah tidak dipakai (Opsional tapi biar bersih)
            $table->dropColumn(['siklus', 'tanggal_panen', 'berat_panen', 'jumlah_ikat', 'kualitas', 'avg_health', 'avg_tds', 'avg_ph', 'avg_suhu', 'avg_kelembapan', 'catatan']);

            // Tambahkan kolom baru untuk kebutuhan tracking AI & Sensor kalian
            $table->string('status_anomali')->after('id_device')->comment('Misal: pH Rendah + Nutrisi Kurang');
            $table->text('rekomendasi_ai')->nullable()->after('status_anomali');
            $table->float('nilai_ph')->nullable()->after('rekomendasi_ai');
            $table->integer('nilai_tds')->nullable()->after('nilai_ph');
            $table->float('nilai_suhu')->nullable()->after('nilai_tds');
            $table->string('status_perbaikan')->default('Pending')->after('nilai_suhu')->comment('Pending / Teratasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika migration di-rollback, kembalikan strukturnya seperti semula
        Schema::table('riwayat_anomali', function (Blueprint $table) {
            $table->dropColumn(['status_anomali', 'rekomendasi_ai', 'nilai_ph', 'nilai_tds', 'nilai_suhu', 'status_perbaikan']);
        });

        Schema::rename('riwayat_anomali', 'riwayat_panen');
    }
};