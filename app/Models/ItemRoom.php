<?php

namespace App\Models;

use App\Enums\ItemCondition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'room_id',
        'quantity',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'condition' => ItemCondition::class,
            'quantity' => 'integer',
        ];
    }

    // ---------- Relationships ----------

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function damageReports(): HasMany
    {
        return $this->hasMany(DamageReport::class);
    }

    // ---------- Scopes ----------

    public function scopeDamaged(Builder $query): Builder
    {
        return $query->whereIn('condition', ['rusak_ringan', 'rusak_berat']);
    }
}
