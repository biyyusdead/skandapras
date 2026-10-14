<?php

namespace App\Console\Commands;

use App\Models\Achievement;
use App\Models\Alumni;
use App\Models\Department;
use App\Models\Extracurricular;
use App\Models\FeaturedProduct;
use App\Models\News;
use App\Models\SchoolProfile;
use App\Models\SchoolRule;
use App\Models\Teacher;
use App\Services\ImageService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ConvertImagesToWebp extends Command
{
    protected $signature = 'images:convert-webp
                            {--model= : Model spesifik (opsional, mis. news)}
                            {--dry-run : Preview saja, tidak eksekusi}';
    protected $description = 'Convert semua gambar ke WebP + update database';

    /**
     * Daftar model + kolom gambar yang perlu di-convert.
     */
    protected array $targets = [
        'achievement'     => ['model' => Achievement::class,      'columns' => ['image']],
        'alumni'          => ['model' => Alumni::class,           'columns' => ['photo']],
        'department'      => ['model' => Department::class,       'columns' => ['image']],
        'extracurricular' => ['model' => Extracurricular::class,  'columns' => ['image']],
        'product'         => ['model' => FeaturedProduct::class,  'columns' => ['image']],
        'news'            => ['model' => News::class,             'columns' => ['image']],
        'rule'            => ['model' => SchoolRule::class,       'columns' => ['image']],
        'teacher'         => ['model' => Teacher::class,          'columns' => ['photo']],
        'school'          => ['model' => SchoolProfile::class,    'columns' => ['logo', 'principal_photo', 'structure_image']],
    ];

    public function handle(): int
    {
        $filter     = $this->option('model');
        $dryRun     = $this->option('dry-run');
        $disk       = Storage::disk('public');
        $totalCount = 0;
        $totalSkip  = 0;
        $totalFail  = 0;

        $targets = $filter
            ? array_filter($this->targets, fn($k) => $k === $filter, ARRAY_FILTER_USE_KEY)
            : $this->targets;

        if (empty($targets)) {
            $this->error("Model '{$filter}' tidak dikenal.");
            $this->line('Model yang tersedia: ' . implode(', ', array_keys($this->targets)));
            return self::FAILURE;
        }

        foreach ($targets as $key => $config) {
            $this->info("\n📦 Memproses: {$key}");
            $model  = $config['model'];
            $columns = $config['columns'];

            foreach ($columns as $column) {
                $this->line("  → Kolom: {$column}");

                $records = $model::query()
                    ->whereNotNull($column)
                    ->where($column, '!=', '')
                    ->get();

                foreach ($records as $record) {
                    $path = $record->$column;

                    // Skip kalau bukan file lokal (URL eksternal) atau sudah webp
                    if (str_starts_with($path, 'http')) {
                        $totalSkip++;
                        continue;
                    }

                    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if ($ext === 'webp') {
                        $totalSkip++;
                        continue;
                    }

                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $totalSkip++;
                        continue;
                    }

                    $fullPath = $disk->path($path);
                    if (!file_exists($fullPath)) {
                        $this->warn("    ✗ File tidak ditemukan: {$path}");
                        $totalFail++;
                        continue;
                    }

                    if ($dryRun) {
                        $this->line("    [DRY-RUN] {$path} → akan di-convert");
                        $totalCount++;
                        continue;
                    }

                    try {
                        // Convert file
                        $uploadedFile = new UploadedFile(
                            $fullPath,
                            basename($path),
                            mime_content_type($fullPath),
                            null,
                            true // test mode
                        );

                        $directory = dirname($path);
                        $newPath   = ImageService::upload($uploadedFile, $directory);

                        // Hapus file lama
                        if ($disk->exists($path)) {
                            $disk->delete($path);
                        }

                        // Update DB dengan path baru
                        $record->update([$column => $newPath]);

                        $this->line("    ✓ {$path} → {$newPath}");
                        $totalCount++;
                    } catch (\Throwable $e) {
                        $this->error("    ✗ {$path}: {$e->getMessage()}");
                        $totalFail++;
                    }
                }
            }
        }

        $this->newLine();
        if ($dryRun) {
            $this->info("🔍 DRY-RUN: {$totalCount} akan dikonvert, {$totalSkip} skip, {$totalFail} gagal");
            $this->line("Jalankan tanpa --dry-run untuk eksekusi.");
        } else {
            $this->info("✅ Selesai: {$totalCount} converted, {$totalSkip} skip, {$totalFail} gagal");
        }

        return self::SUCCESS;
    }
}