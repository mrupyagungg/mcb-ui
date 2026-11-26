<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id',
        'user_id',
        'region',
        'assigned_by',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Kategori anggota diambil otomatis dari users.position.
     */
    public function getKategoriAttribute(): string
    {
        $position = strtoupper(trim($this->user->position ?? ''));

        return match ($position) {
            'DOCUMENT', 'DOCUMENT CONTROLLER' => 'Document',
            'DRAFTER' => 'Drafter',
            'PERMIT' => 'PERMIT',
            'SURVEYOR/WASPANG', 'SURVEYOR', 'WASPANG' => 'SURVEYOR/WASPANG',
            'LOGISTIC', 'LOGISTICS' => 'Logistic',
            'MATERIAL CONTROL' => 'Material Control',
            'ADMIN' => 'Admin',
            'COORD PROJECT' => 'Coord Project',
            'COORD LOGISTIC' => 'Coord Logistic',
            default => $this->user->position ?? 'Belum ditentukan',
        };
    }

}