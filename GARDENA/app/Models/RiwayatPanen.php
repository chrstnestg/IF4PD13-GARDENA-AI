<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPanen extends Model
{
    protected $table    = 'riwayat_panen';

    protected $fillable = [
        'id_user', 'id_device', 'siklus', 'tanggal_panen',
        'berat_panen', 'jumlah_ikat', 'kualitas', 'avg_health',
        'avg_tds', 'avg_ph', 'avg_suhu', 'avg_kelembapan', 'catatan',
    ];

    protected $casts = [
        'tanggal_panen' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function perangkat()
    {
        return $this->belongsTo(PerangkatIot::class, 'id_device', 'id_device');
    }
}