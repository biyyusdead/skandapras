<?php

namespace App\Enums;

enum DamageReportStatus: string
{
    case MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    case DIVERIFIKASI = 'diverifikasi';
    case MENUNGGU_PERSETUJUAN_1 = 'menunggu_persetujuan_1';
    case MENUNGGU_PERSETUJUAN_2 = 'menunggu_persetujuan_2';
    case DISETUJUI = 'disetujui';
    case SIAP_DIPERBAIKI = 'siap_diperbaiki';
    case DIPROSES = 'diproses';
    case SELESAI = 'selesai';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::DIVERIFIKASI => 'Diverifikasi',
            self::MENUNGGU_PERSETUJUAN_1 => 'Menunggu Persetujuan 1',
            self::MENUNGGU_PERSETUJUAN_2 => 'Menunggu Persetujuan 2',
            self::DISETUJUI => 'Disetujui',
            self::SIAP_DIPERBAIKI => 'Siap Diperbaiki',
            self::DIPROSES => 'Diproses',
            self::SELESAI => 'Selesai',
            self::DITOLAK => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI,
            self::MENUNGGU_PERSETUJUAN_1,
            self::MENUNGGU_PERSETUJUAN_2 => 'yellow',
            self::DIVERIFIKASI => 'blue',
            self::DISETUJUI,
            self::SIAP_DIPERBAIKI => 'purple',
            self::DIPROSES => 'indigo',
            self::SELESAI => 'green',
            self::DITOLAK => 'red',
        };
    }

    /**
     * Status yang menandakan laporan masih dalam proses approval.
     */
    public static function pendingApproval(): array
    {
        return [
            self::MENUNGGU_VERIFIKASI,
            self::DIVERIFIKASI,
            self::MENUNGGU_PERSETUJUAN_1,
            self::MENUNGGU_PERSETUJUAN_2,
        ];
    }

    /**
     * Status yang menandakan laporan siap/boleh masuk perbaikan.
     */
    public static function allowRepair(): array
    {
        return [
            self::DISETUJUI,
            self::SIAP_DIPERBAIKI,
        ];
    }
}
