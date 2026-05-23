<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table      = 'notifikasi';
    protected $primaryKey = 'id_notif';
    protected $fillable   = [
        'id_user', 'id_analisis', 'pesan', 'status_baca', 'waktu_kirim'
    ];

    protected $casts = [
        'status_baca'  => 'boolean',
        'waktu_kirim'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function analisisAi()
    {
        return $this->belongsTo(AnalisisAi::class, 'id_analisis', 'id_analisis');
    }
}