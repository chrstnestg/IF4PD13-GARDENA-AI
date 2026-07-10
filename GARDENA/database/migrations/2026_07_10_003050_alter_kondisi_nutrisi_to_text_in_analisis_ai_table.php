<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analisis_ai', function (Blueprint $table) {
            $table->text('kondisi_nutrisi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('analisis_ai', function (Blueprint $table) {
            $table->string('kondisi_nutrisi', 255)->change();
        });
    }
};