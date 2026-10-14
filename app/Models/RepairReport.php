<?php

namespace App\Models;

use App\Enums\RepairStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'damage_report_id',
        'technician_name',
        'technician_contact',
        'repair_description',
        'repair_cost',
        'started_at',
        'completed_at',
        'repair_evidence_before',
        'repair_evidence_after',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status'       => RepairStatus::class,
            'repair_cost'  => 'decimal:2',
            'started_at'   => 'date',
            'completed_at' => 'date',
        ];
    }

    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === RepairStatus::COMPLETED;
    }
}