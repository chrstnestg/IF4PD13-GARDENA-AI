<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    protected $table      = 'data_sensor';
    protected $primaryKey = 'id_sensor';
    protected $fillable   = [
        'id_device', 'ph', 'suhu', 'ec_tds', 'kelembapan',
        'status_valid', 'dibaca_pada'
    ];

    protected $casts = [
        'dibaca_pada'  => 'datetime',
        'status_valid' => 'boolean',
    ];

    public function perangkat()
    {
        return $this->belongsTo(PerangkatIot::class, 'id_device', 'id_device');
    }

    public function analisisAi()
    {
        return $this->hasOne(AnalisisAi::class, 'id_sensor', 'id_sensor');
    }
}