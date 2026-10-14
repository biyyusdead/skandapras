<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;

class ImageService
{
    /**
     * Default max width untuk resize (px).
     */
    public const DEFAULT_MAX_WIDTH = 1600;

    /**
     * Default WebP quality (0-100).
     */
    public const DEFAULT_QUALITY = 80;

    /**
     * Process uploaded image → convert ke WebP + resize + compress.
     * Return path relatif dari storage public.
     *
     * @param  UploadedFile  $file
     * @param  string        $folder         Folder tujuan (mis. "news", "alumni")
     * @param  int           $maxWidth       Max width untuk resize
     * @param  int           $quality        WebP quality (0-100)
     * @param  string|null   $oldPath        Path lama untuk dihapus
     * @return string        Path file baru
     */
    public static function upload(
        UploadedFile $file,
        string $folder,
        int $maxWidth = self::DEFAULT_MAX_WIDTH,
        int $quality = self::DEFAULT_QUALITY,
        ?string $oldPath = null
    ): string {
        // Hapus file lama kalau ada
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Buat nama file unik dengan ekstensi .webp
        $filename = Str::random(40) . '.webp';
        $path = "{$folder}/{$filename}";

        // Init Intervention Image
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getRealPath());

        // Resize kalau lebih besar dari maxWidth (jaga aspect ratio)
        if ($image->width() > $maxWidth) {
            $image->scaleDown(width: $maxWidth);
        }

        // Encode ke WebP dengan quality
        $encoded = $image->toWebp($quality);

        // Simpan ke storage/public
        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    /**
     * Upload dengan ukuran khusus untuk thumbnail.
     */
    public static function uploadThumb(
        UploadedFile $file,
        string $folder,
        int $maxWidth = 400,
        int $quality = 75,
        ?string $oldPath = null
    ): string {
        return self::upload($file, $folder . '/thumbs', $maxWidth, $quality, $oldPath);
    }

    /**
     * Hapus file dari storage.
     */
    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}