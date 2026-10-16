<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'achievement_date',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'achievement_date' => 'date',
        ];
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        // Kalau URL full (http), langsung pakai
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // Kalau path lokal, konversi ke storage URL
        return asset('storage/'.$this->image);
    }
}
