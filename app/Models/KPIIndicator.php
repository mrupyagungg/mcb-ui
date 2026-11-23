<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPIIndicator extends Model
{
    protected $table = 'kpi_indicators';

    protected $fillable = [
        'nama',
        'kategori',
        'jenis',
        'bobot',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'bobot' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}