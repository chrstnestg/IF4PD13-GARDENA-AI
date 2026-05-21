<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TindakanRekomendasi extends Model
{
    protected $table = 'tindakan_rekomendasi';

    protected $fillable = ['rekomendasi_id', 'aksi', 'dilakukan_pada'];

    protected $casts = [
        'dilakukan_pada' => 'datetime',
    ];

    public function rekomendasi()
    {
        return $this->belongsTo(Rekomendasi::class);
    }
}