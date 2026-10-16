<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    // ---------- Relationships ----------

    public function roomUsers(): HasMany
    {
        return $this->hasMany(RoomUser::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_users')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'reporter_id');
    }

    public function verifiedDamageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class, 'verified_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function repairReports(): HasMany
    {
        return $this->hasMany(RepairReport::class, 'created_by');
    }

    // ---------- Helpers ----------

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function isWakaSarpras(): bool
    {
        return $this->role === UserRole::WAKA_SARPRAS;
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === UserRole::KEPALA_SEKOLAH;
    }

    public function isPenggunaRuangan(): bool
    {
        return $this->role === UserRole::PENGGUNA_RUANGAN;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role->value, $roles, true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
