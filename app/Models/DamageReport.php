<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\DamageCategory;
use App\Enums\DamageReportStatus;
use App\Enums\IdentityStatus;
use App\Enums\ReporterType;
use App\Enums\ValidityStatus;
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
        'item_room_id',
        'reporter_id',
        'reporter_type',
        'reporter_name',
        'reporter_phone',
        'reporter_identifier',
        'student_class',
        'student_major',
        'description',
        'evidence_photo',
        'reported_at',
        'status',
        'identity_status',
        'identity_note',
        'validity_status',
        'rejection_reason',
        'damage_category',
        'category_note',
        'verification_note',
        'verified_at',
        'verified_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => DamageReportStatus::class,
            'reporter_type' => ReporterType::class,
            'identity_status' => IdentityStatus::class,
            'validity_status' => ValidityStatus::class,
            'damage_category' => DamageCategory::class,
            'reported_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $report) {
            if (empty($report->report_code)) {
                $report->report_code = static::generateReportCode();
            }
            if (empty($report->reported_at)) {
                $report->reported_at = now();
            }
        });
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

    public function displayName(): string
    {
        return $this->reporter_name ?: ($this->reporter?->name ?? 'Pengunjung Umum');
    }

    public function displayPhone(): ?string
    {
        return $this->reporter_phone ?: $this->reporter?->phone;
    }

    public function displayIdentifier(): ?string
    {
        return $this->reporter_identifier;
    }

    public function maskedIdentifier(): string
    {
        $id = $this->reporter_identifier;
        if (! $id) {
            return '—';
        }

        $length = strlen($id);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        if ($length <= 8) {
            return substr($id, 0, 2).str_repeat('*', $length - 4).substr($id, -2);
        }

        // Contoh NIP (18 digit): 19800202******2002
        return substr($id, 0, 8).str_repeat('*', max(2, $length - 12)).substr($id, -4);
    }

    public function maskedPhone(): string
    {
        $phone = $this->displayPhone();
        if (! $phone) {
            return '—';
        }

        $length = strlen($phone);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return substr($phone, 0, 4).str_repeat('*', max(2, $length - 7)).substr($phone, -3);
    }

    public function isIdentityVerified(): bool
    {
        return $this->identity_status === IdentityStatus::TERVERIFIKASI;
    }

    public function isValidated(): bool
    {
        return $this->validity_status === ValidityStatus::VALID;
    }

    public function isMinorDamage(): bool
    {
        return $this->damage_category === DamageCategory::RINGAN;
    }

    public function isMajorDamage(): bool
    {
        return $this->damage_category === DamageCategory::BERAT;
    }

    /**
     * Cek apakah laporan boleh masuk proses perbaikan.
     * Syarat:
     * - Identitas terverifikasi & Laporan dinyatakan valid.
     * - Kerusakan Ringan: Cukup persetujuan Waka (Level 1).
     * - Kerusakan Besar: Membutuhkan persetujuan Waka (Level 1) & Kepala Sekolah (Level 2).
     */
    public function canBeRepaired(): bool
    {
        // Untuk laporan baru (yang memiliki validity_status & identity_status)
        if ($this->validity_status && $this->validity_status !== ValidityStatus::VALID) {
            return false;
        }

        if ($this->identity_status && $this->identity_status !== IdentityStatus::TERVERIFIKASI) {
            return false;
        }

        if ($this->damage_category === DamageCategory::RINGAN) {
            return $this->isApprovedLevel1();
        }

        if ($this->damage_category === DamageCategory::BERAT) {
            return $this->isApprovedLevel1() && $this->isApprovedLevel2();
        }

        // Fallback untuk data lama tanpa kategori
        return $this->isApprovedLevel1() && $this->isApprovedLevel2();
    }

    /**
     * Generate kode laporan unik: RPT-YYYYMMDD-XXXX
     */
    public static function generateReportCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "RPT-{$date}-";
        $last = static::where('report_code', 'like', "{$prefix}%")
            ->orderByDesc('report_code')
            ->value('report_code');

        $number = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
