<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerangkatIot extends Model
{
    protected $table      = 'perangkat_iot';
    protected $primaryKey = 'id_device';
    protected $fillable   = ['id_user', 'nama_device', 'status_aktif'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function dataSensor()
    {
        return $this->hasMany(DataSensor::class, 'id_device', 'id_device');
    }

    public function riwayatPanen()
    {
        return $this->hasMany(RiwayatPanen::class, 'id_device', 'id_device');
    }
}