<?php

namespace App\Policies;

use App\Enums\ApprovalStatus;
use App\Models\Approval;
use App\Models\User;

class ApprovalPolicy
{
    /**
     * Lihat daftar approval yang butuh tindakan.
     */
    public function viewAny(User $user): bool
    {
        return $user->isWakaSarpras() || $user->isKepalaSekolah();
    }

    /**
     * Lihat detail approval.
     */
    public function view(User $user, Approval $approval): bool
    {
        // Waka hanya level 1, Kepsek hanya level 2
        if ($user->isWakaSarpras() && $approval->approval_level === 1) {
            return true;
        }

        if ($user->isKepalaSekolah() && $approval->approval_level === 2) {
            return true;
        }

        return false;
    }

    /**
     * Approve/reject.
     */
    public function decide(User $user, Approval $approval): bool
    {
        if ($approval->status !== ApprovalStatus::PENDING) {
            return false;
        }

        return $this->view($user, $approval);
    }
}