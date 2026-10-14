<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — {{ config('app.school_name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
    @if ($logo)
        <link rel="icon" href="{{ asset('storage/' . $logo) }}">
    @endif

    <style>
        .wave-divider {
            position: absolute;
            top: 0;
            right: -1px;
            height: 100%;
            width: 120px;
            z-index: 5;
        }
        @media (max-width: 1023px) {
            .wave-divider { display: none; }
        }
        .input-underline {
            border: none;
            border-bottom: 1.5px solid #d4d4d8;
            border-radius: 0;
            padding-left: 0;
            padding-right: 0;
            background: transparent;
            transition: border-color 0.2s ease;
        }
        .input-underline:focus {
            outline: none;
            border-bottom-color: #6C4AB6;
            box-shadow: none;
        }
        .input-underline::placeholder {
            color: #a1a1aa;
            font-size: 13px;
        }
    </style>
</head>
<body class="min-h-screen bg-white font-sans antialiased lg:h-screen lg:overflow-hidden">

<div class="min-h-screen grid grid-cols-1 lg:h-screen lg:grid-cols-2">

    {{-- ============================================================ --}}
    {{-- PANEL BRANDING --}}
    {{-- Mobile: di atas, ringkas (logo + nama saja) --}}
    {{-- Desktop: di kiri, full --}}
    {{-- ============================================================ --}}
    <div class="relative bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900
                text-white p-6 sm:p-8 lg:p-16 flex flex-col justify-between gap-8
                lg:order-1 lg:h-full">

        {{-- Decorative blobs --}}
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-20 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Wave divider (desktop only) --}}
        <svg class="wave-divider" viewBox="0 0 120 800" preserveAspectRatio="none" fill="white">
            <path d="M120,0 L120,800 L70,800 C110,700 50,600 90,500 C130,400 50,300 90,200 C130,100 50,50 70,0 Z"></path>
        </svg>

        {{-- Atas: sapaan + logo + nama sekolah --}}
        <div class="relative z-10">
            <p class="text-base sm:text-lg font-medium text-white/90 tracking-wide text-center lg:text-left">
                Selamat Datang di
            </p>

            <div class="mt-6 sm:mt-8 lg:mt-12 flex flex-col items-center lg:items-start text-center lg:text-left">
                @php $schoolLogo = \App\Models\SchoolProfile::current()?->logo; @endphp

                @if ($schoolLogo)
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20
                                flex items-center justify-center p-3 mb-4">
                        <img src="{{ asset('storage/' . $schoolLogo) }}" alt="Logo Sekolah"
                             class="w-full h-full object-contain drop-shadow-lg">
                    </div>
                @else
                    <div class="w-16 h-16 sm:w-20 sm:h-20 lg:w-24 lg:h-24 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20
                                flex items-center justify-center mb-4">
                        <i data-lucide="graduation-cap" class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 text-white"></i>
                    </div>
                @endif

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight">SKANDAPRAS</h1>
                <p class="text-xs sm:text-sm lg:text-base text-primary-200/80 mt-2">
                    {{ \App\Models\SchoolProfile::current()?->school_name ?? config('app.school_name') }}
                </p>
            </div>
        </div>

        {{-- Tengah: deskripsi (desktop only) --}}
        <div class="relative z-10 max-w-md mx-auto lg:mx-0 text-center lg:text-left hidden lg:block">
            <p class="text-xs sm:text-sm lg:text-base text-primary-100/70 leading-relaxed">
                Sistem Informasi Sekolah dan Manajemen Sarana Prasarana.
                Kelola data sekolah, ruangan, barang, dan laporan kerusakan dalam satu platform terpadu.
            </p>
        </div>

        {{-- Bawah: footer link (desktop only) --}}
        <div class="relative z-10 hidden lg:flex items-center justify-center lg:justify-start gap-6 text-xs text-primary-200/60">
            <a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a>
            <span class="w-1 h-1 rounded-full bg-primary-300/40"></span>
            <a href="#" class="hover:text-white transition-colors">Bantuan</a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- PANEL FORM --}}
    {{-- Mobile: di bawah --}}
    {{-- Desktop: di kanan --}}
    {{-- ============================================================ --}}
    <div class="lg:order-2 flex items-center justify-center p-6 sm:p-8 lg:p-16 lg:h-full">

        <div class="w-full max-w-sm">

            <div class="mb-6 sm:mb-8">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-surface-900 tracking-tight">Login</h2>
                <p class="text-xs sm:text-sm text-surface-500 mt-2">
                    Masukkan kredensial akun Anda untuk melanjutkan
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5 sm:space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-medium text-surface-500 uppercase tracking-wide mb-2">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           placeholder="nama@sekolah.sch.id"
                           class="input-underline w-full py-2 text-sm text-surface-900
                                  @error('email') !border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-surface-500 uppercase tracking-wide mb-2">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="Masukkan password Anda"
                           class="input-underline w-full py-2 text-sm text-surface-900
                                  @error('password') !border-red-400 @enderror">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember"
                           class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                    <label for="remember" class="ml-2 text-xs sm:text-sm text-surface-600">Ingat saya di perangkat ini</label>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-primary-600 hover:bg-primary-700
                                   text-white text-sm font-semibold rounded-full
                                   transition-all duration-200 ease-smooth
                                   shadow-md hover:shadow-lg active:scale-[0.98]
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                        Login
                    </button>
                    <a href="{{ url('/') }}"
                       class="flex-1 text-center py-3 px-4
                              border-2 border-surface-200 hover:border-primary-400 hover:text-primary-600
                              text-surface-600 text-sm font-semibold rounded-full
                              transition-all duration-200 ease-smooth">
                        Beranda
                    </a>
                </div>
            </form>

            <p class="text-center text-[11px] sm:text-xs text-surface-400 mt-6 sm:mt-8 leading-relaxed">
                Dengan login, Anda menyetujui
                <a href="#" class="text-primary-600 hover:text-primary-700 font-medium">Syarat & Ketentuan</a> yang berlaku.
            </p>

        </div>
    </div>

</div>

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

</body>
</html>