<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name', 'npsn', 'nss', 'address', 'postal_code', 'phone', 'fax',
        'email', 'principal_name', 'accreditation', 'education_level', 'status',
        'latitude', 'longitude', 'elevation', 'study_hours',
        'description', 'vision', 'mission',
        'principal_message', 'principal_photo',
        'history', 'structure_image', 'logo',
    ];

    /**
     * Misisi sebagai array (dipisah newline).
     */
    public function getMissionsAttribute(): array
    {
        if (empty($this->mission)) {
            return [];
        }

        return array_filter(array_map('trim', explode("\n", $this->mission)));
    }

    protected static ?self $memoizedCurrent = null;

    protected static function booted(): void
    {
        static::saved(function () {
            static::clearCurrentCache();
        });

        static::deleted(function () {
            static::clearCurrentCache();
        });
    }

    /**
     * Clear cached school profile.
     */
    public static function clearCurrentCache(): void
    {
        static::$memoizedCurrent = null;
    }

    /**
     * Ambil profil sekolah (single record) dengan per-request memoization.
     */
    public static function current(): ?self
    {
        if (static::$memoizedCurrent !== null) {
            return static::$memoizedCurrent;
        }

        return static::$memoizedCurrent = static::first();
    }
}
