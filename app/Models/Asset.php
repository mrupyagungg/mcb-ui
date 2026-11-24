<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Asset extends Model
{
    protected $table = 'assets';

    protected $fillable = [
        'asset_code',
        'category',
        'brand',
        'model',
        'serial_number',
        'assigned_to',
        'area',
        'location',
        'vendor',
        'status',
        'condition',
        'description',
        'notes',
        'photo',
        'acquisition_date',
        'purchase_price',
        'warranty_expiry',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    // Karyawan pemegang aset

    // Admin yang membuat data
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Admin yang terakhir memperbarui data
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }
    public function pic()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}