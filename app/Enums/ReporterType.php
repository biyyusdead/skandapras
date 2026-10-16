<?php

namespace App\Enums;

enum ReporterType: string
{
    case SISWA = 'siswa';
    case GURU_TENDIK = 'guru_tendik';
    case PENGUNJUNG = 'pengunjung';

    public function label(): string
    {
        return match ($this) {
            self::SISWA => 'Siswa',
            self::GURU_TENDIK => 'Guru / Tenaga Kependidikan',
            self::PENGUNJUNG => 'Pengunjung Umum',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SISWA => 'blue',
            self::GURU_TENDIK => 'purple',
            self::PENGUNJUNG => 'slate',
        };
    }
}
