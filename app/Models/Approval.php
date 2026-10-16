<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Approval extends Model
{
    use HasFactory;

    protected $fillable = [
        'damage_report_id',
        'approver_id',
        'approval_level',
        'status',
        'notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ApprovalStatus::class,
            'approval_level' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function levelLabel(): string
    {
        return match ($this->approval_level) {
            1 => 'Waka Sarpras',
            2 => 'Kepala Sekolah',
            default => '-',
        };
    }
}
