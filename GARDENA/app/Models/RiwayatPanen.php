<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatPanen extends Model
{
    // 1. Hubungkan ke nama tabel database yang baru hasil migration
    protected $table = 'riwayat_anomali';

    // 2. Sesuaikan amunisi kolom baru untuk mencatat data anomali sensor & AI
    protected $fillable = [
        'id_user', 
        'id_device', 
        'status_anomali',   // Hasil gabungan hybrid dari Python (misal: "pH Rendah + Nutrisi Kurang")
        'rekomendasi_ai',   // List tindakan penanganan langsung dari FastAPI Python
        'nilai_ph',         // Nilai pH real-time saat kejadian
        'nilai_tds',        // Nilai TDS real-time saat kejadian
        'nilai_suhu',       // Nilai suhu real-time saat kejadian
        'status_perbaikan', // Status 'Pending' atau 'Teratasi'
    ];

    // Kolom tanggal_panen sudah dihapus, jadi protected $casts lama dibuang saja

    /* ─────────────────────────────────────────
     | Relasi Database
     ───────────────────────────────────────── */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function perangkat()
    {
        return $this->belongsTo(PerangkatIot::class, 'id_device', 'id_device');
    }
}