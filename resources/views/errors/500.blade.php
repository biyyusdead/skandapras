<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Terjadi Kesalahan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gradient-to-br from-red-50 via-white to-orange-50
             font-sans text-surface-800 antialiased">

<div class="min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full text-center animate-fade-in-up">

        {{-- Logo --}}
        @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
        <div class="flex justify-center mb-8">
            @if ($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Logo"
                     class="w-20 h-20 object-contain drop-shadow-md">
            @else
                <div class="w-20 h-20 rounded-2xl bg-red-100 flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="w-10 h-10 text-red-600"></i>
                </div>
            @endif
        </div>

        {{-- Big Number --}}
        <div class="relative mb-6">
            <p class="text-[120px] leading-none font-bold text-red-100 select-none">500</p>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 rounded-2xl bg-white shadow-lg ring-1 ring-surface-200/60
                            flex items-center justify-center">
                    <i data-lucide="server-crash" class="w-10 h-10 text-red-600"></i>
                </div>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-surface-900 mb-2 tracking-tight">
            Terjadi Kesalahan Server
        </h1>
        <p class="text-sm text-surface-500 leading-relaxed mb-8 max-w-sm mx-auto">
            Maaf, server sedang mengalami gangguan. Tim teknis kami sudah diberitahu.
            Silakan coba beberapa saat lagi atau hubungi administrator.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="location.reload()"
                    class="inline-flex items-center gap-2 px-5 py-2.5
                           bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg
                           transition-all duration-200 ease-smooth shadow-sm hover:shadow-md active:scale-[0.98]">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Coba Lagi
            </button>
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5
                      border border-surface-300 hover:border-primary-400 hover:bg-primary-50
                      text-surface-700 text-sm font-medium rounded-lg
                      transition-all duration-200 ease-smooth">
                <i data-lucide="home" class="w-4 h-4"></i>
                Kembali ke Beranda
            </a>
        </div>

        {{-- Error ID (untuk debugging) --}}
        @if (isset($exception) && $exception->getMessage())
            <details class="mt-8 text-left max-w-sm mx-auto">
                <summary class="text-xs text-surface-400 cursor-pointer hover:text-surface-600 text-center">
                    Detail teknis
                </summary>
                <div class="mt-3 p-3 bg-surface-100 rounded-lg text-xs text-surface-600 font-mono break-all">
                    {{ $exception->getMessage() }}
                </div>
            </details>
        @endif

        {{-- Footer --}}
        <p class="text-xs text-surface-400 mt-10">
            &copy; {{ date('Y') }} {{ \App\Models\SchoolProfile::current()?->school_name ?? config('app.school_name') }}
        </p>

    </div>

</div>

</body>
</html>