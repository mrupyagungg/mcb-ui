<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen_at',
        'role',
        'position',
        'vendor',
        'region',
        'no_rekening',
        'bank',
        'phone',
        'id_number',
        'employee_id',
        'start_date',
        'jenis_kelamin',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime',
        'start_date' => 'date',
        'jenis_kelamin' => 'string',
        'alamat' => 'string',
    ];

    public function getInitialsAttribute()
    {
        $name = trim($this->name);
        $words = explode(' ', $name);

        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($name, 0, 2));
    }

    public function isOnline()
    {
        return $this->last_seen_at &&
            $this->last_seen_at->gt(now()->subMinutes(5));
    }
    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }
}