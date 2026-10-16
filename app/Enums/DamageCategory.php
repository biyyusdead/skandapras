<?php

namespace App\Enums;

enum DamageCategory: string
{
    case RINGAN = 'ringan';
    case BERAT = 'berat';

    public function label(): string
    {
        return match ($this) {
            self::RINGAN => 'Kerusakan Ringan',
            self::BERAT => 'Kerusakan Besar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::RINGAN => 'teal',
            self::BERAT => 'rose',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::RINGAN => 'Kerusakan kecil yang tidak membutuhkan anggaran besar. Dapat disetujui & ditugaskan langsung oleh Waka Sarpras.',
            self::BERAT => 'Kerusakan signifikan/biaya besar yang membutuhkan persetujuan Kepala Sekolah.',
        };
    }
}
