<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN            = 'admin';
    case WAKA_SARPRAS     = 'waka_sarpras';
    case KEPALA_SEKOLAH   = 'kepala_sekolah';
    case PENGGUNA_RUANGAN = 'pengguna_ruangan';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN            => 'Administrator',
            self::WAKA_SARPRAS     => 'Waka Sarpras',
            self::KEPALA_SEKOLAH   => 'Kepala Sekolah',
            self::PENGGUNA_RUANGAN => 'Pengguna Ruangan',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}