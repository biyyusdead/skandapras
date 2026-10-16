<?php

namespace App\Enums;

enum ValidityStatus: string
{
    case MENUNGGU_PEMERIKSAAN = 'menunggu_pemeriksaan';
    case VALID = 'valid';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMERIKSAAN => 'Menunggu Pemeriksaan',
            self::VALID => 'Laporan Valid',
            self::DITOLAK => 'Laporan Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MENUNGGU_PEMERIKSAAN => 'yellow',
            self::VALID => 'emerald',
            self::DITOLAK => 'red',
        };
    }
}
