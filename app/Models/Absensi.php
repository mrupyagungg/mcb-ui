<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_masuk',
        'latitude_masuk',
        'longitude_masuk',
        'jalan_masuk',
        'kota_masuk',
        'foto_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'jalan_keluar',
        'kota_keluar',
        'foto_keluar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}