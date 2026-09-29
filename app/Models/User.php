<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{

    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'last_seen_at',
        'role'
    ];


    protected $hidden = [
        'password',
        'remember_token'
    ];


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen_at' => 'datetime', // <--- Agar bisa dibaca sebagai tanggal/waktu
        ];
    }

    // Tambahkan di dalam class User:
    public function getInitialsAttribute()
    {
        $name = $this->name;
        $words = explode(' ', trim($name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($name, 0, 2));
    }
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen_at' => 'datetime', // Pastikan ini ada
    ];

    // Fungsi untuk mengecek status online
    public function isOnline()
    {
        // Jika last_seen_at ada dan selisih waktunya kurang dari 5 menit yang lalu
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }

}