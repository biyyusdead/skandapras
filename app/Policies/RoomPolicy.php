<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Admin & Waka Sarpras bisa melakukan semua aksi.
     */
    public function before(User $user, string $ability): ?bool
    {
        // Hanya Waka Sarpras yang bypass penuh.
        // Admin TIDAK menangani Sarpras.
        if ($user->isWakaSarpras()) {
            return true;
        }

        return null;
    }

    /**
     * Semua user login bisa melihat daftar ruangan.
     * Pengguna Ruangan hanya boleh lihat ruangan miliknya (dicek di viewAll/ownership).
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Lihat detail ruangan.
     * Kepala Sekolah bisa lihat semua (monitoring).
     * Pengguna Ruangan hanya ruangan miliknya.
     */
    public function view(User $user, Room $room): bool
    {
        if ($user->isKepalaSekolah()) {
            return true;
        }

        if ($user->isPenggunaRuangan()) {
            return $room->isOwnedBy($user->id);
        }

        return false;
    }

    /**
     * Hanya Waka Sarpras (dan Admin) yang bisa create/update/delete.
     * (Sudah dihandle `before()`, method ini hanya untuk fallback.)
     */
    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Room $room): bool
    {
        return false;
    }

    public function delete(User $user, Room $room): bool
    {
        return false;
    }
}