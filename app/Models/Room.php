<?php

namespace App\Models;

use App\Enums\RoomCondition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'floor',
        'capacity',
        'description',
        'condition',
        'health_score',
        'photo',
        'panorama_url',
        'panorama_file',
        'is_active',
        'map_x', 'map_y', 'map_floor',
        'model_3d_x', 'model_3d_y', 'model_3d_w', 'model_3d_h',
        'model_3d_height', 'model_3d_shape', 'model_3d_color', 'model_3d_visible',
    ];

    protected $casts = [
        'condition'         => RoomCondition::class,
        'is_active'         => 'boolean',
        'health_score'      => 'integer',
        'capacity'          => 'integer',
        'map_x'             => 'float',
        'map_y'             => 'float',
        'model_3d_x'        => 'float',
        'model_3d_y'        => 'float',
        'model_3d_w'        => 'float',
        'model_3d_h'        => 'float',
        'model_3d_height'   => 'float',
        'model_3d_visible'  => 'boolean',
    ];

    protected $appends = ['photo_url', 'detail_url', 'report_url'];

    // ---------- Relationships ----------

    public function roomUsers(): HasMany
    {
        return $this->hasMany(RoomUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_users')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    // HAPUS method items() yang lama
    // TAMBAHKAN:

    public function itemRooms(): HasMany
    {
        return $this->hasMany(ItemRoom::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_rooms')
            ->withPivot(['quantity', 'condition', 'notes'])
            ->withTimestamps();
    }

    /**
     * Health score berbobot quantity.
     */
    public function recalculateHealthScore(): int
    {
        $rows = $this->itemRooms()->get();

        if ($rows->isEmpty()) {
            return 100;
        }

        $totalWeight  = 0;
        $earnedWeight = 0;

        foreach ($rows as $row) {
            $q = $row->quantity;
            $totalWeight += $q;

            $earnedWeight += match ($row->condition->value) {
                'baik'         => $q,
                'rusak_ringan' => $q * 0.5,
                'rusak_berat'  => 0,
                default        => 0,
            };
        }

        return $totalWeight > 0
            ? (int) round(($earnedWeight / $totalWeight) * 100)
            : 100;
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    // ---------- Scopes ----------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->whereHas('roomUsers', fn ($q) => $q->where('user_id', $userId));
    }

    // ---------- Helpers ----------

    public function hasPanorama(): bool
    {
        return !empty($this->panorama_file) || !empty($this->panorama_url);
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->roomUsers()->where('user_id', $userId)->exists();
    }

    /**
 * URL panorama final — prioritas file upload, fallback ke URL manual.
    */
    public function getPanoramaAttribute(): ?string
    {
        if ($this->panorama_file) {
            return asset('storage/' . $this->panorama_file);
        }

        return $this->panorama_url ?: null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }

    public function getDetailUrlAttribute(): string
    {
        return route('public.rooms.show', $this->code);
    }

    public function getReportUrlAttribute(): string
    {
        return route('public.sarpras.pengaduan', ['room_id' => $this->id]);
    }
}