<?php

namespace App\Helpers;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\AppNotification;

class NotificationHelper
{
    /**
     * Kirim notifikasi ke semua user dengan role tertentu.
     */
    public static function toRole(UserRole|string $role, string $title, string $message, ?string $url = null, string $icon = 'bell', string $color = 'primary'): void
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;

        User::where('role', $roleValue)
            ->where('is_active', true)
            ->get()
            ->each(fn ($user) => $user->notify(
                new AppNotification($title, $message, $url, $icon, $color)
            ));
    }

    /**
     * Kirim notifikasi ke user tertentu.
     */
    public static function toUser(?User $user, string $title, string $message, ?string $url = null, string $icon = 'bell', string $color = 'primary'): void
    {
        if (! $user) {
            \Log::info('[Notif] Skip — user null: '.$title);

            return;
        }

        $user->notify(new AppNotification($title, $message, $url, $icon, $color));
    }
}
