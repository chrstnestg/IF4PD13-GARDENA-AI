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
        // 1. Matikan pengecekan foreign key sementara
        Schema::disableForeignKeyConstraints();

        // 2. Hapus kolom di data_sensor JIKA kolomnya masih ada
        if (Schema::hasColumn('data_sensor', 'id_device')) {
            Schema::table('data_sensor', function (Blueprint $table) {
                $table->dropForeign(['id_device']); 
                $table->dropColumn('id_device');
            });
        }

        // 3. Hapus kolom di riwayat_anomali JIKA kolomnya masih ada
        if (Schema::hasColumn('riwayat_anomali', 'id_device')) {
            Schema::table('riwayat_anomali', function (Blueprint $table) {
                $table->dropForeign('riwayat_panen_id_device_foreign');
                $table->dropColumn('id_device');
            });
        }

        // 4. Hapus tabel utama
        Schema::dropIfExists('perangkat_iot');

        // 5. Aktifkan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 
    }
};