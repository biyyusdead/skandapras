<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Alumni extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit (karena "alumni" bukan bentuk jamak standar Laravel).
     */
    protected $table = 'alumni';

    protected $fillable = [
        'name',
        'slug',
        'photo',
        'graduation_year',
        'department',
        'achievement',
        'motto',
        'description',
    ];

    protected $casts = [
        'graduation_year' => 'integer',
    ];

    /**
     * Auto-generate slug dari name kalau slug belum diisi.
     */
    protected static function booted(): void
{
    static::creating(function (self $alumni) {
        if (empty($alumni->slug)) {
            $alumni->slug = static::generateUniqueSlug($alumni->name);
        }
    });

    static::updating(function (self $alumni) {
        // Kalau nama berubah, slug juga ikut berubah
        // (kecuali slug diubah manual lewat form)
        if ($alumni->isDirty('name') && ! $alumni->isDirty('slug')) {
            $alumni->slug = static::generateUniqueSlug($alumni->name);
        }
    });
}

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Scope: urutkan berdasarkan tahun lulus terbaru, lalu nama.
     */
    public function scopeOrdered($query)
    {
        return $query->orderByDesc('graduation_year')->orderBy('name');
    }

    /**
     * Accessor: URL foto.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}