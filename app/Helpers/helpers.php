<?php

use App\Models\SchoolProfile;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (! function_exists('school_name')) {
    function school_name(): string
    {
        return SchoolProfile::first()?->school_name
            ?? config('app.school_name', 'SMK Negeri 2 Karanganyar');
    }
}

if (! function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return match ($role) {
            'admin' => 'Administrator',
            'waka_sarpras' => 'Waka Sarpras',
            'kepala_sekolah' => 'Kepala Sekolah',
            'pengguna_ruangan' => 'Pengguna Ruangan',
            default => '-',
        };
    }
}

if (! function_exists('abort_unless_owner')) {
    /**
     * Abort 403 jika user bukan owner dari resource.
     */
    function abort_unless_owner(bool $condition, string $message = 'Anda tidak berhak mengakses data ini.'): void
    {
        abort_unless($condition, 403, $message);
    }
}

if (! function_exists('safe_route')) {
    /**
     * Route helper yang tidak error kalau route belum ada.
     */
    function safe_route(string $name, array $params = []): string
    {
        return Route::has($name)
            ? route($name, $params)
            : '#';
    }
}

if (! function_exists('is_route_active')) {
    /**
     * Cek apakah route saat ini cocok dengan pattern (untuk active state menu).
     */
    function is_route_active(?string $pattern): bool
    {
        if (! $pattern) {
            return false;
        }

        return request()->routeIs($pattern);
    }
}

if (! function_exists('handle_upload')) {
    /**
     * Handle upload + auto-convert ke WebP + resize.
     *
     * @param  UploadedFile|null  $file
     */
    function handle_upload($file, string $folder, ?string $oldPath = null, int $maxWidth = 1600, int $quality = 80): ?string
    {
        if (! $file) {
            return $oldPath;
        }

        // Kalau file bukan gambar (mis. PDF), simpan biasa
        if (! str_starts_with($file->getMimeType() ?? '', 'image/')) {
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return $file->store($folder, 'public');
        }

        return ImageService::upload($file, $folder, $maxWidth, $quality, $oldPath);
    }
}

if (! function_exists('avatar_color')) {
    /**
     * Warna solid konsisten berdasarkan nama user.
     */
    function avatar_color(string $name): string
    {
        $colors = [
            '#DC2626', // red-600
            '#EA580C', // orange-600
            '#D97706', // amber-600
            '#CA8A04', // yellow-600
            '#65A30D', // lime-600
            '#16A34A', // green-600
            '#059669', // emerald-600
            '#0891B2', // cyan-600
            '#0284C7', // sky-600
            '#2563EB', // blue-600
            '#4F46E5', // indigo-600
            '#7C3AED', // violet-600
            '#9333EA', // purple-600
            '#C026D3', // fuchsia-600
            '#DB2777', // pink-600
            '#E11D48', // rose-600
            '#475569', // slate-600
            '#78716C', // stone-600
        ];

        $hash = crc32($name);

        return $colors[abs($hash) % count($colors)];
    }
}

if (! function_exists('avatar_initials')) {
    /**
     * Ambil 1-2 huruf inisial dari nama.
     */
    function avatar_initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));

        if (count($parts) >= 2) {
            return strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[1], 0, 1));
        }

        return strtoupper(mb_substr($name, 0, 2));
    }
}
