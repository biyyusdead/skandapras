<?php

namespace App\Policies;

use App\Enums\DamageReportStatus;
use App\Models\DamageReport;
use App\Models\User;

class DamageReportPolicy
{
    /**
     * Admin tidak menangani Sarpras → tidak boleh akses.
     * Waka Sarpras & Kepala Sekolah boleh semua.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isWakaSarpras() || $user->isKepalaSekolah()) {
            return true;
        }

        return null;
    }

    /**
     * Pengguna Ruangan: hanya laporan miliknya.
     * Admin: ditolak (di-handle oleh before yang return null lalu cek di sini).
     */
    public function viewAny(User $user): bool
    {
        return $user->isPenggunaRuangan();
    }

    public function view(User $user, DamageReport $report): bool
    {
        if ($user->isPenggunaRuangan()) {
            return $report->reporter_id === $user->id;
        }

        return false;
    }

    /**
     * Pengguna Ruangan boleh membuat laporan.
     */
    public function create(User $user): bool
    {
        return $user->isPenggunaRuangan();
    }

    /**
     * Pengguna Ruangan hanya boleh edit laporan yang masih MENUNGGU_VERIFIKASI.
     */
    public function update(User $user, DamageReport $report): bool
    {
        if (! $user->isPenggunaRuangan()) {
            return false;
        }

        return $report->reporter_id === $user->id
            && $report->status === DamageReportStatus::MENUNGGU_VERIFIKASI;
    }

    /**
     * Hanya boleh hapus laporan yang masih MENUNGGU_VERIFIKASI milik sendiri.
     */
    public function delete(User $user, DamageReport $report): bool
    {
        return $this->update($user, $report);
    }
}
