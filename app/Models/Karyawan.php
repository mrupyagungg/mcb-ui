<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Karyawan extends Authenticatable
{
    protected $fillable = [
        'kota_id',
        'nama',
        'id_karyawan',
        'tanggal_lahir',
        'username',
        'tanggal_masuk',
        'email',
        'jenis_kelamin',
        'no_hp',
        'jabatan',
        'status',
        'foto'
    ];

    protected $hidden = [
        'password'
    ];

    public function kota()
    {
        return $this->belongsTo(Kota::class);
    }
}