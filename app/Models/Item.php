<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'brand',
        'description',
        'photo',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ---------- Relationships ----------

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function itemRooms(): HasMany
    {
        return $this->hasMany(ItemRoom::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'item_rooms')
            ->withPivot(['quantity', 'condition', 'notes'])
            ->withTimestamps();
    }

    // ---------- Scopes ----------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ---------- Helpers ----------

    /**
     * Total unit dari semua ruangan.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->itemRooms->sum('quantity');
    }

    /**
     * Jumlah ruangan yang memiliki barang ini.
     */
    public function getRoomCountAttribute(): int
    {
        return $this->itemRooms->count();
    }
}
