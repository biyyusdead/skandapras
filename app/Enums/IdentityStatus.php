<?php

namespace App\Enums;

enum IdentityStatus: string
{
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case TERVERIFIKASI = 'terverifikasi';
    case PERLU_KLARIFIKASI = 'perlu_klarifikasi';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::PERLU_KLARIFIKASI => 'Perlu Klarifikasi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'yellow',
            self::TERVERIFIKASI => 'green',
            self::PERLU_KLARIFIKASI => 'orange',
        };
    }
}
