<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>404 — Halaman Tidak Ditemukan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-16 h-16 rounded-2xl bg-yellow-50 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="file-question" class="w-8 h-8 text-yellow-600"></i>
        </div>
        <h1 class="text-3xl font-bold text-surface-900">404</h1>
        <p class="text-surface-500 mt-2">{{ $exception->getMessage() ?: 'Halaman yang Anda cari tidak ditemukan.' }}</p>
        <div class="mt-6">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg">
                <i data-lucide="home" class="w-4 h-4"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>