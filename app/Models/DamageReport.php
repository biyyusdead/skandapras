<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\DamageReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DamageReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_code',
        'room_id',
        'item_id',
        'reporter_id',
        'description',
        'evidence_photo',
        'reported_at',
        'status',
        'verification_note',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'status'       => DamageReportStatus::class,
            'reported_at'  => 'datetime',
            'verified_at'  => 'datetime',
        ];
    }

    // ---------- Relationships ----------

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function itemRoom(): BelongsTo
    {
        return $this->belongsTo(ItemRoom::class);
    }

    public function getItemAttribute(): ?Item
    {
        return $this->itemRoom?->item;
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class)->orderBy('approval_level');
    }

    public function repairReports(): HasMany
    {
        return $this->hasMany(RepairReport::class);
    }

    public function latestRepair(): ?RepairReport
    {
        return $this->repairReports()->latest()->first();
    }

    // ---------- Scopes ----------

    public function scopeByReporter(Builder $query, int $userId): Builder
    {
        return $query->where('reporter_id', $userId);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            fn ($s) => $s->value,
            DamageReportStatus::pendingApproval()
        ));
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->whereIn('status', [
            DamageReportStatus::DISETUJUI->value,
            DamageReportStatus::SIAP_DIPERBAIKI->value,
            DamageReportStatus::DIPROSES->value,
            DamageReportStatus::SELESAI->value,
        ]);
    }

    // ---------- Helpers ----------

    public function approvalLevel1(): ?Approval
    {
        return $this->approvals()->where('approval_level', 1)->first();
    }

    public function approvalLevel2(): ?Approval
    {
        return $this->approvals()->where('approval_level', 2)->first();
    }

    public function isApprovedLevel1(): bool
    {
        return $this->approvalLevel1()?->status === ApprovalStatus::APPROVED;
    }

    public function isApprovedLevel2(): bool
    {
        return $this->approvalLevel2()?->status === ApprovalStatus::APPROVED;
    }

    /**
     * Cek apakah laporan boleh masuk proses perbaikan.
     * Syarat: approval level 1 & 2 approved.
     */
    public function canBeRepaired(): bool
    {
        return $this->isApprovedLevel1() && $this->isApprovedLevel2();
    }

    /**
     * Generate kode laporan unik: RPT-YYYYMMDD-XXXX
     */
    public static function generateReportCode(): string
    {
        $date   = now()->format('Ymd');
        $prefix = "RPT-{$date}-";
        $last   = static::where('report_code', 'like', "{$prefix}%")
            ->orderByDesc('report_code')
            ->value('report_code');

        $number = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}