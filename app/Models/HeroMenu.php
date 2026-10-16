<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class HeroMenu extends Model
{
    protected $fillable = [
        'label',
        'title',
        'description',
        'icon',
        'route_name',
        'color_from',
        'color_to',
        'pattern',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope: hanya yang aktif, urut by order.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    /**
     * Helper: route URL yang aman (fallback ke # kalau route tidak ada).
     */
    public function getUrlAttribute(): string
    {
        return Route::has($this->route_name)
            ? route($this->route_name)
            : '#';
    }
}
