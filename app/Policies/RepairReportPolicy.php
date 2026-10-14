<?php

namespace App\Policies;

use App\Models\RepairReport;
use App\Models\User;

class RepairReportPolicy
{
    /**
     * Hanya Waka Sarpras yang boleh CRUD repair.
     * Kepala Sekolah boleh view (monitoring).
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isWakaSarpras()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isKepalaSekolah();
    }

    public function view(User $user, RepairReport $repair): bool
    {
        return $user->isKepalaSekolah();
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, RepairReport $repair): bool
    {
        return false;
    }

    public function delete(User $user, RepairReport $repair): bool
    {
        return false;
    }
}