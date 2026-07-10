<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSensor extends Model
{
    protected $table      = 'data_sensor';
    protected $fillable   = [
        'ph', 'suhu', 'ec_tds',
        'status_valid', 'dibaca_pada'
    ];

    protected $casts = [
        'dibaca_pada'  => 'datetime',
        'status_valid' => 'boolean',
    ];

    public function analisisAi()
    {
        return $this->hasOne(AnalisisAi::class, 'id_sensor', 'id_sensor');
    }
}