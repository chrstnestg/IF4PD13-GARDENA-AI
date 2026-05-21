<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';

    protected $fillable = [
        'nutrisi_id', 'judul', 'status', 'label_status',
        'nilai_saat_ini', 'nilai_optimal', 'deskripsi',
        'aksi_list', 'kritis', 'pesan_kritis',
    ];

    protected $casts = [
        'aksi_list' => 'array',
        'kritis'    => 'boolean',
    ];

    public function tindakan()
    {
        return $this->hasMany(TindakanRekomendasi::class);
    }
}