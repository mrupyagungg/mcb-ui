<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'module',
        'action_type',
        'region',
        'target_name',
        'description',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}