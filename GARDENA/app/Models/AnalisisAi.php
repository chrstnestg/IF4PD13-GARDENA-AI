<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalisisAi extends Model
{
    protected $table      = 'analisis_ai';
    protected $primaryKey = 'id_analisis';
    protected $fillable   = [
        'id_sensor', 
        'kondisi_nutrisi', 
        'rekomendasi', 
        'waktu_analisis',
        'status_tindakan',  // ← tambah ini
    ];

    protected $casts = [
        'waktu_analisis' => 'datetime',
    ];

    public function dataSensor()
    {
        return $this->belongsTo(DataSensor::class, 'id_sensor', 'id_sensor');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_analisis', 'id_analisis');
    }
}