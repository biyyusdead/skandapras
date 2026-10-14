<?php

namespace App\Enums;

enum RepairStatus: string
{
    case READY       = 'ready';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED   = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::READY       => 'Siap',
            self::IN_PROGRESS => 'Diproses',
            self::COMPLETED   => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::READY       => 'yellow',
            self::IN_PROGRESS => 'indigo',
            self::COMPLETED   => 'green',
        };
    }
}