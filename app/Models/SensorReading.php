<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    protected $fillable = [
        'perangkat', 'ec', 'ph', 'suhu', 'kelembapan', 'dibaca_pada'
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    // Ambil data 7 hari terakhir per hari
    public static function tujuhHariTerakhir(): array
    {
        $hasil = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $rata = self::whereDate('dibaca_pada', $tanggal)->avg('ec') ?? 0;
            $hasil[] = round($rata, 2);
        }
        return $hasil;
    }

    // Ambil rata-rata semua sensor hari ini
    public static function rataHariIni(): ?self
    {
        $data = self::whereDate('dibaca_pada', today())->get();
        if ($data->isEmpty()) return null;

        $obj = new self();
        $obj->ec         = round($data->avg('ec'), 2);
        $obj->ph         = round($data->avg('ph'), 2);
        $obj->suhu       = round($data->avg('suhu'), 2);
        $obj->kelembapan = round($data->avg('kelembapan'), 2);
        return $obj;
    }
}