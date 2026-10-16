<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — {{ \App\Models\SchoolProfile::current()?->school_name ?? config('app.school_name') }}</title>
    <meta name="description" content="@yield('description', \App\Models\SchoolProfile::current()?->description ?? '')">
    @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
    @if ($logo)
        <link rel="icon" href="{{ asset('storage/' . $logo) }}">
    @endif

    @vite([
        'resources/css/app.css',
        'resources/css/public.css',
        'resources/js/app.js',
        'resources/js/public.js'
    ])
    @stack('styles')
</head>

<body class="relative bg-surface-50 text-surface-800 font-sans antialiased pb-20 lg:pb-0">

    {{-- ============================================================ --}}
    {{-- GLOBAL DECORATIVE BACKGROUND --}}
    {{-- ============================================================ --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        {{-- Base gradient --}}
        <div class="absolute inset-0 bg-gradient-to-b from-surface-50 via-white to-surface-50"></div>

        {{-- Grid pattern halus --}}
        <div class="absolute inset-0 opacity-[0.03]"
             style="background-image:
                linear-gradient(to right, #6C4AB6 1px, transparent 1px),
                linear-gradient(to bottom, #6C4AB6 1px, transparent 1px);
                background-size: 64px 64px;">
        </div>

        {{-- Dot pattern --}}
        <div class="absolute inset-0 opacity-[0.025]"
             style="background-image: radial-gradient(circle, #6C4AB6 1px, transparent 1px);
                    background-size: 32px 32px;"></div>

        {{-- Gradient mesh blobs --}}
        <div class="absolute -top-40 left-1/4 w-[600px] h-[600px] rounded-full
                    bg-primary-200/25 blur-[120px] animate-float"></div>
        <div class="absolute top-1/3 -right-40 w-[500px] h-[500px] rounded-full
                    bg-accent-amber/10 blur-[120px] animate-float-delay"></div>
        <div class="absolute -bottom-40 -left-40 w-[550px] h-[550px] rounded-full
                    bg-primary-300/20 blur-[120px] animate-float"></div>

        {{-- Floating rings --}}
        <div class="absolute top-[15%] right-[8%] w-24 h-24 rounded-full border border-primary-200/40 animate-spin-slow"></div>
        <div class="absolute top-[60%] left-[5%] w-16 h-16 rounded-full border border-dashed border-accent-amber/30 animate-spin-slow-reverse"></div>

        {{-- Floating plus signs --}}
        <svg class="absolute top-[25%] left-[12%] w-4 h-4 text-primary-300/50 animate-float" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" d="M12 4v16m8-8H4"/>
        </svg>
        <svg class="absolute top-[70%] right-[15%] w-3 h-3 text-accent-amber/50 animate-float-delay" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" d="M12 4v16m8-8H4"/>
        </svg>
    </div>

    {{-- ============================================================ --}}
    {{-- SCROLL PROGRESS BAR --}}
    {{-- ============================================================ --}}
    <div id="scroll-progress"
         class="fixed top-0 left-0 h-0.5 bg-gradient-to-r from-primary-500 via-primary-600 to-accent-amber
                z-[100] origin-left scale-x-0
                transition-transform duration-100 ease-out"></div>

    <x-preloader />
    <x-loading-bar />

    {{-- ============================================================ --}}
    {{-- CONTENT WRAPPER (z-10 agar di atas decorative bg) --}}
    {{-- ============================================================ --}}
    <div class="relative z-10">

        {{-- ============================================================ --}}
        {{-- NAVBAR --}}
        {{-- ============================================================ --}}
        <nav x-data="{ scrolled: false, mobileOpen: false }"
             x-on:scroll.window="scrolled = window.scrollY > 20"
             :class="scrolled
                 ? 'bg-primary-600/95 shadow-lg shadow-primary-900/20'
                 : 'bg-primary-600'"
             class="sticky top-0 z-50 w-full transition-all duration-300">
            <div class="max-w-[1440px] mx-auto px-6 lg:px-12">
                <div :class="scrolled ? 'py-3' : 'py-5'" class="flex items-center justify-between transition-all duration-300">

                    {{-- Logo --}}
                    <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0 group">
                        @php $schoolLogo = \App\Models\SchoolProfile::current()?->logo; @endphp
                        @if ($schoolLogo)
                            <img src="{{ asset('storage/' . $schoolLogo) }}"
                                width="40" height="40"
                                class="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                                alt="Logo {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}">
                        @else
                            <img src="{{ asset('assets/images/logo/logo.webp') }}"
                                width="40" height="40"
                                class="h-10 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                                alt="Logo SMK Negeri 2 Karanganyar">
                        @endif
                        <span class="block text-[10px] sm:text-xs font-bold text-white leading-tight tracking-wide">
                            <span class="sm:hidden">SMK Negeri 2 <br>
                                <span class="text-white/70 font-medium tracking-[0.15em] text-[10px]">KARANGANYAR</span>
                            </span>
                            <span class="hidden sm:block">SMK NEGERI 2<br>
                                <span class="text-white/70 font-medium tracking-[0.15em] text-[10px]">KARANGANYAR</span>
                            </span>
                        </span>
                    </a>

                    {{-- Menu desktop --}}
                    <div class="hidden lg:flex items-center gap-1">
                        @php
                            $isTentang = request()->routeIs('public.profile.*');
                            $isSarpras = request()->routeIs('public.rooms.*', 'public.sarpras.*');
                            $isAkademik = request()->routeIs('public.akademik.*');
                            $isKesiswaan = request()->routeIs('public.kesiswaan.*');
                            $isNews = request()->routeIs('public.news.*');
                        @endphp

                        {{-- Dropdown Tentang --}}
                        <div x-data="{ open: false }" class="relative" x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                            <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-full
                                           transition-colors duration-200
                                           {{ $isTentang ? 'text-white bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                                Tentang
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2.5" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute left-0 top-full pt-2 w-64 z-50">
                                <div class="bg-white rounded-2xl shadow-dropdown border border-surface-200 overflow-hidden p-2">
                                    <a href="{{ route('public.profile.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Identitas Sekolah
                                    </a>
                                    <a href="{{ route('public.profile.sambutan') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Sambutan Kepala Sekolah
                                    </a>
                                    <a href="{{ route('public.profile.sejarah') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Sejarah Sekolah
                                    </a>
                                    <a href="{{ route('public.profile.struktur') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Struktur Organisasi
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Sarpras --}}
                        <div x-data="{ open: false }" class="relative" x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                            <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-full
                                           transition-colors duration-200
                                           {{ $isSarpras ? 'text-white bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                                Sarpras
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2.5" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute left-0 top-full pt-2 w-64 z-50">
                                <div class="bg-white rounded-2xl shadow-dropdown border border-surface-200 overflow-hidden p-2">
                                    <a href="{{ route('public.rooms.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Daftar Ruangan
                                    </a>
                                    <a href="{{ route('public.rooms.map') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Peta Sekolah
                                    </a>
                                    <a href="{{ route('public.sarpras.pengaduan') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Buat Laporan
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Akademik --}}
                        <div x-data="{ open: false }" class="relative" x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                            <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-full
                                           transition-colors duration-200
                                           {{ $isAkademik ? 'text-white bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                                Akademik
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2.5" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute left-0 top-full pt-2 w-72 z-50">
                                <div class="bg-white rounded-2xl shadow-dropdown border border-surface-200 overflow-hidden p-2">
                                    <a href="{{ route('public.akademik.guru.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Guru & Tenaga Kependidikan
                                    </a>
                                    <a href="{{ route('public.akademik.kompetensi.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Kompetensi Keahlian
                                    </a>
                                    <a href="{{ route('public.akademik.spmb.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        SPMB
                                    </a>
                                    <a href="{{ route('public.akademik.produk.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Produk Unggulan
                                    </a>
                                    <a href="{{ route('public.akademik.alumni.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Alumni
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Dropdown Kesiswaan --}}
                        <div x-data="{ open: false }" class="relative" x-on:mouseenter="open = true" x-on:mouseleave="open = false">
                            <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-full
                                           transition-colors duration-200
                                           {{ $isKesiswaan ? 'text-white bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                                Kesiswaan
                                <svg class="w-3 h-3 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2.5" d="m19 9-7 7-7-7"/>
                                </svg>
                            </button>
                            <div x-show="open" x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute left-0 top-full pt-2 w-64 z-50">
                                <div class="bg-white rounded-2xl shadow-dropdown border border-surface-200 overflow-hidden p-2">
                                    <a href="{{ route('public.kesiswaan.prestasi.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Prestasi
                                    </a>
                                    <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Ekstrakurikuler
                                    </a>
                                    <a href="{{ route('public.kesiswaan.tata-tertib.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        Tata Tertib
                                    </a>
                                    <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}"
                                       class="block px-4 py-2.5 text-sm rounded-xl text-surface-700 hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                        BKK & PKL
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Berita --}}
                        <a href="{{ route('public.news.index') }}"
                           class="px-4 py-2 text-sm font-medium rounded-full transition-colors duration-200
                                  {{ $isNews ? 'text-white bg-white/15' : 'text-white/90 hover:text-white hover:bg-white/10' }}">
                            Berita
                        </a>
                    </div>

                    {{-- CTA kanan --}}
                    <div class="hidden lg:flex items-center gap-2">
                        @guest
                            <a href="{{ route('public.akademik.spmb.index') }}"
                               class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-full
                                      bg-white text-primary-700 text-sm font-semibold
                                      hover:bg-accent-amber hover:text-surface-900
                                      transition-all duration-300 hover:shadow-lg">
                                Daftar SPMB
                                <i data-lucide="arrow-right"
                                   class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-0.5"></i>
                            </a>
                        @endguest

                        @auth
                            @php
                                $dashboardRoute = match (auth()->user()->role->value) {
                                    'admin' => 'admin.dashboard',
                                    'waka_sarpras' => 'waka.dashboard',
                                    'kepala_sekolah' => 'kepala.dashboard',
                                    'pengguna_ruangan' => 'pengguna.dashboard',
                                    default => 'public.home',
                                };
                            @endphp

                            <a href="{{ route($dashboardRoute) }}"
                               class="px-4 py-2 text-sm font-semibold text-white
                                      hover:bg-white/10 rounded-full transition-colors">
                                Dashboard
                            </a>

                            <div x-data="{ open: false }" class="relative" x-on:click.outside="open = false">
                                <button type="button" x-on:click="open = !open"
                                        class="flex items-center gap-2 p-1 pl-3 rounded-full
                                               bg-white/10 hover:bg-white/20 transition-colors">
                                    <span class="text-sm font-medium text-white hidden xl:inline">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white ring-2 ring-white/40"
                                          style="background-color: {{ avatar_color(auth()->user()->name) }};">
                                        {{ avatar_initials(auth()->user()->name) }}
                                    </span>
                                </button>

                                <div x-show="open" x-cloak
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute right-0 mt-2 w-72 z-50 bg-white rounded-2xl shadow-dropdown
                                            border border-surface-200 overflow-hidden">
                                    <div class="px-4 py-3 bg-surface-50 border-b border-surface-200">
                                        <p class="text-sm font-semibold text-surface-900 truncate">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-surface-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full
                                                         bg-primary-100 text-primary-700
                                                         text-[10px] font-bold uppercase tracking-wide">
                                                {{ auth()->user()->role->label() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="py-1">
                                        <a href="{{ route($dashboardRoute) }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-surface-700
                                                  hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-primary-600"></i>
                                            Dashboard
                                        </a>
                                        <a href="{{ route('settings.index') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-surface-700
                                                  hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                            <i data-lucide="user" class="w-4 h-4 text-primary-600"></i>
                                            Profil Saya
                                        </a>
                                        <a href="{{ route('notifications.index') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-surface-700
                                                  hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                            <i data-lucide="bell" class="w-4 h-4 text-primary-600"></i>
                                            Notifikasi
                                            @if (auth()->user()->unreadNotifications->count() > 0)
                                                <span class="ml-auto inline-flex items-center justify-center
                                                             min-w-[18px] h-[18px] px-1.5 rounded-full
                                                             bg-red-500 text-white text-[10px] font-semibold">
                                                    {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                                                </span>
                                            @endif
                                        </a>
                                        <a href="{{ route('public.home') }}"
                                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-surface-700
                                                  hover:bg-primary-50 hover:text-primary-600 transition-colors">
                                            <i data-lucide="globe" class="w-4 h-4 text-primary-600"></i>
                                            Landing Page
                                        </a>
                                    </div>

                                    <div class="border-t border-surface-200 py-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm
                                                           text-red-600 hover:bg-red-50 transition-colors">
                                                <i data-lucide="log-out" class="w-4 h-4 text-red-500"></i>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endauth
                    </div>

                    {{-- Mobile toggle --}}
                    <button type="button" x-on:click="mobileOpen = !mobileOpen"
                            class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-full
                                   text-white hover:bg-white/10 transition-colors"
                            aria-label="Toggle menu">
                        <svg class="w-5 h-5" :class="mobileOpen ? 'hidden' : 'block'"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                  d="M5 7h14M5 12h14M5 17h14"/>
                        </svg>
                        <svg class="w-5 h-5" x-show="mobileOpen" x-cloak
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                                  d="m6 6 12 12M6 18 18 6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile menu --}}
            <div x-show="mobileOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="lg:hidden absolute top-full left-0 right-0 bg-white
                        border-t border-surface-200 shadow-dropdown max-h-[80vh] overflow-y-auto">

                <div class="px-6 py-4 space-y-4">

                    {{-- Tentang --}}
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer py-2
                                        font-semibold text-surface-900 hover:text-primary-600 transition-colors">
                            Tentang
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="pl-4 mt-1 space-y-1 border-l border-surface-200">
                            <a href="{{ route('public.profile.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Identitas Sekolah</a>
                            <a href="{{ route('public.profile.sambutan') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Sambutan Kepala Sekolah</a>
                            <a href="{{ route('public.profile.sejarah') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Sejarah Sekolah</a>
                            <a href="{{ route('public.profile.struktur') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Struktur Organisasi</a>
                        </div>
                    </details>

                    {{-- Sarpras --}}
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer py-2
                                        font-semibold text-surface-900 hover:text-primary-600 transition-colors">
                            Sarpras
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="pl-4 mt-1 space-y-1 border-l border-surface-200">
                            <a href="{{ route('public.rooms.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Daftar Ruangan</a>
                            <a href="{{ route('public.rooms.map') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Peta Sekolah</a>
                            <a href="{{ route('public.sarpras.pengaduan') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Buat Laporan</a>
                        </div>
                    </details>

                    {{-- Akademik --}}
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer py-2
                                        font-semibold text-surface-900 hover:text-primary-600 transition-colors">
                            Akademik
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="pl-4 mt-1 space-y-1 border-l border-surface-200">
                            <a href="{{ route('public.akademik.guru.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Guru & Tenaga Kependidikan</a>
                            <a href="{{ route('public.akademik.kompetensi.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Kompetensi Keahlian</a>
                            <a href="{{ route('public.akademik.spmb.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">SPMB</a>
                            <a href="{{ route('public.akademik.produk.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Produk Unggulan</a>
                            <a href="{{ route('public.akademik.alumni.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Alumni</a>
                        </div>
                    </details>

                    {{-- Kesiswaan --}}
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer py-2
                                        font-semibold text-surface-900 hover:text-primary-600 transition-colors">
                            Kesiswaan
                            <svg class="w-4 h-4 transition-transform group-open:rotate-180"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m19 9-7 7-7-7"/>
                            </svg>
                        </summary>
                        <div class="pl-4 mt-1 space-y-1 border-l border-surface-200">
                            <a href="{{ route('public.kesiswaan.prestasi.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Prestasi</a>
                            <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Ekstrakurikuler</a>
                            <a href="{{ route('public.kesiswaan.tata-tertib.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">Tata Tertib</a>
                            <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}"
                               class="block py-2 text-sm text-surface-700 hover:text-primary-600">BKK & PKL</a>
                        </div>
                    </details>

                    {{-- Berita --}}
                    <a href="{{ route('public.news.index') }}"
                       class="block py-2 font-semibold text-surface-900 hover:text-primary-600 transition-colors">
                        Berita
                    </a>

                    {{-- CTA mobile --}}
                    <div class="pt-4 border-t border-surface-200 space-y-2">
                        @guest
                            <a href="{{ route('public.akademik.spmb.index') }}"
                               class="block w-full py-3 text-center text-sm font-semibold text-white
                                      bg-primary-600 hover:bg-primary-700 rounded-full transition-colors">
                                Daftar SPMB
                            </a>
                        @endguest

                        @auth
                            @php
                                $dashboardRouteMobile = match (auth()->user()->role->value) {
                                    'admin' => 'admin.dashboard',
                                    'waka_sarpras' => 'waka.dashboard',
                                    'kepala_sekolah' => 'kepala.dashboard',
                                    'pengguna_ruangan' => 'pengguna.dashboard',
                                    default => 'public.home',
                                };
                            @endphp
                            <a href="{{ route($dashboardRouteMobile) }}"
                               class="block w-full py-3 text-center text-sm font-semibold text-white
                                      bg-primary-600 hover:bg-primary-700 rounded-full transition-colors">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full py-3 text-center text-sm font-semibold text-red-600
                                               border border-red-200 rounded-full hover:bg-red-50 transition-colors">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- ============================================================ --}}
        {{-- CONTENT --}}
        {{-- ============================================================ --}}
        <main>
            @yield('content')
        </main>

        {{-- ============================================================ --}}
        {{-- FOOTER --}}
        {{-- ============================================================ --}}
        <footer class="relative overflow-hidden rounded-t-[60px] bg-primary-600 text-white px-4 pt-8 pb-4 sm:px-8">


            {{-- CONTENT UTAMA --}}
            <div class="relative z-10 mx-auto flex max-w-6xl flex-col items-center justify-between gap-8 p-2 sm:p-4 lg:flex-row lg:gap-10">

                {{-- KIRI --}}
                <div class="w-full lg:w-1/2">
                    <img src="{{ asset('assets/images/logo/footer.webp') }}"
                        onerror="this.onerror=null;this.style.display='none';"
                        alt="Logo SMK Bisa Hebat"
                        class="mb-4 h-auto object-contain" style="width: 50%; max-width: 256px; min-width: 160px;">

                    <p class="max-w-md text-sm leading-relaxed text-white/90 sm:text-[15px]">
                        {{ \App\Models\SchoolProfile::current()?->description ?? 'SMK Negeri 2 Karanganyar adalah salah satu Sekolah Menengah Kejuruan favorit di Kabupaten Karanganyar. Serta merupakan sekolah yang berorientasi pada karakter, berwawasan, disiplin, tanggung jawab, dan berdaya saing.' }}
                    </p>

                    <div class="mt-4 space-y-2 text-xs text-white/90 sm:text-[14px]">
                        <p class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>{{ \App\Models\SchoolProfile::current()?->address ?? 'Jl. Yos Sudarso, Karanganyar' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i data-lucide="mail" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span class="break-words">{{ \App\Models\SchoolProfile::current()?->email ?? 'smkn2kra@yahoo.co.id' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i data-lucide="phone" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>{{ \App\Models\SchoolProfile::current()?->phone ?? '0271-4950xxx' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>Jam Kerja: Senin - Jumat, 07:00 - 15:00 WIB</span>
                        </p>
                    </div>
                </div>

                <div x-data="{ mapLoaded: false }"
                    class="h-64 w-full overflow-hidden rounded-2xl sm:h-80 lg:w-1/2 border border-white/20 relative">

                    {{-- Thumbnail + Tombol --}}
                    <div x-show="!mapLoaded"
                        @click="mapLoaded = true"
                        class="absolute inset-0 cursor-pointer group">
                        {{-- Background pattern --}}
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-700 to-primary-900"></div>
                        <div class="absolute inset-0 opacity-10"
                            style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;"></div>

                        {{-- Icon & Text --}}
                        <div class="relative h-full flex flex-col items-center justify-center text-white gap-3">
                            <div class="w-14 h-14 rounded-full bg-white/15 backdrop-blur-sm ring-1 ring-white/30
                                        flex items-center justify-center group-hover:scale-110 transition-transform">
                                <i class="bi bi-geo-alt-fill text-2xl"></i>
                            </div>
                            <div class="text-center px-4">
                                <p class="text-sm font-bold">Lihat Lokasi Sekolah</p>
                                <p class="text-xs text-white/70 mt-1">Klik untuk membuka Google Maps</p>
                            </div>
                        </div>
                    </div>

                    {{-- Iframe (load saat klik) --}}
                    <template x-if="mapLoaded">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.859730598725!2d110.94815151415202!3d-7.590242277103364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e654b99ab219bfd%3A0x4e63f4d5cebe448a!2sSMKN%202%20Karanganyar!5e0!3m2!1sen!2sid!4v1618149814210!5m2!1sen!2sid"
                            width="600" height="450" style="border:0" title="School Map"
                            class="h-full w-full absolute inset-0">
                        </iframe>
                    </template>
                </div>
            </div>

            

            {{-- GARIS --}}
            <div class="relative z-10 mx-auto mt-5 h-px max-w-7xl bg-white/20"></div>

            {{-- LOGO PERLOMBAAN --}}
            <div class="relative z-10 mx-auto mt-5 flex max-w-4xl flex-wrap items-center justify-center
                        gap-5 rounded-xl bg-white shadow-md ring-1 ring-black/5
                        px-5 py-3 sm:gap-8">
                @foreach (['logoJHIC.webp', 'logoJagoanHosting.webp', 'logoKomdigi.webp', 'logoGarudaSpark.webp', 'logoNgalup.webp'] as $logoFile)
                    <img src="{{ asset('assets/images/jhic/' . $logoFile) }}"
                        onerror="this.style.display='none';"
                        alt="{{ $logoFile }}" class="h-9 w-auto object-contain sm:h-11" loading="lazy">
                @endforeach
            </div>

            {{-- SOCIAL MEDIA --}}
            <div class="relative z-10 mt-5 flex flex-wrap justify-center gap-5">
                <a href="https://www.linkedin.com/school/smkn2karanganyar"
                class="text-2xl text-white/70 transition hover:-translate-y-1 hover:text-accent-amber"
                target="_blank" rel="noopener" aria-label="LinkedIn">
                    <i class="bi bi-linkedin"></i>
                </a>

                <a href="https://www.instagram.com/smkn2kra"
                class="text-2xl text-white/70 transition hover:-translate-y-1 hover:text-accent-amber"
                target="_blank" rel="noopener" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>

                <a href="https://www.youtube.com/@smkn2kra"
                class="text-2xl text-white/70 transition hover:-translate-y-1 hover:text-accent-amber"
                target="_blank" rel="noopener" aria-label="YouTube">
                    <i class="bi bi-youtube"></i>
                </a>

                <a href="https://www.tiktok.com/@smkn2kra"
                class="text-2xl text-white/70 transition hover:-translate-y-1 hover:text-accent-amber"
                target="_blank" rel="noopener" aria-label="TikTok">
                    <i class="bi bi-tiktok"></i>
                </a>

                <a href="https://www.facebook.com/smkn2kra"
                class="text-2xl text-white/70 transition hover:-translate-y-1 hover:text-accent-amber"
                target="_blank" rel="noopener" aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>
            </div>

            {{-- COPYRIGHT --}}
            <p class="relative z-10 mt-2 px-2 text-center text-sm text-white/90 sm:text-base">
                Copyright © {{ date('Y') }} <b class="text-white">SMK Negeri 2 Karanganyar.</b> All Rights Reserved.
            </p>

            {{-- DEVELOPED BY & MODAL (Alpine.js Integrated) --}}
            <div x-data="{ teamModalOpen: false }" class="relative z-20 mt-1 flex justify-center">
                <div class="flex flex-col items-center gap-1 text-center text-white">
                    <p class="text-xs sm:text-sm">
                        <span class="font-medium">Developed By</span>
                        <button type="button"
                                @click="teamModalOpen = true"
                                title="Klik Untuk Melihat Detail Tim"
                                class="cursor-pointer text-sm font-bold text-white underline transition hover:text-accent-amber">
                            FIVESTACK-SKANDAPRAS
                        </button>
                    </p>
                </div>

                {{-- MAIN MODAL (Team FIVESTACK) --}}
                <div x-show="teamModalOpen"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @keydown.escape.window="teamModalOpen = false"
                     class="fixed inset-0 z-[100] flex items-center justify-center p-3 sm:p-4 bg-black/60 backdrop-blur-sm"
                     role="dialog" aria-modal="true" aria-labelledby="fivestack-modal-title">

                    <div @click.away="teamModalOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="relative my-auto w-full max-w-3xl">

                        <div class="relative max-h-[90vh] overflow-y-auto overflow-x-hidden rounded-2xl bg-white shadow-2xl">
                            <div class="flex items-center justify-between border-b border-surface-100 px-4 py-4 sm:px-6 sm:py-5">
                                <h3 id="fivestack-modal-title" class="text-lg font-bold text-surface-900 sm:text-xl">FIVESTACK</h3>
                                <button type="button" @click="teamModalOpen = false"
                                        aria-label="Tutup modal"
                                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-surface-400 transition hover:bg-surface-100 hover:text-surface-900">
                                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="px-4 pb-8 pt-6 sm:px-6 sm:pb-9 sm:pt-8">
                                <div class="mb-8 text-center">
                                    <h2 class="text-xl font-bold text-surface-900 sm:text-2xl">
                                        Meet Our <span class="text-primary-600">Team</span>
                                    </h2>
                                    <p class="mx-auto mt-2 max-w-md text-xs text-surface-500 sm:text-sm">
                                        Kenali anggota tim FIVESTACK yang berkontribusi dalam pengembangan website ini.
                                    </p>
                                    <div class="mx-auto mt-4 h-1 w-12 rounded-full bg-primary-600"></div>
                                </div>

                                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 sm:gap-8">
                                    @foreach ([['name' => 'Abiyyu', 'role' => 'Backend Developer', 'file' => 'abiyyu.webp'], ['name' => 'Lintang', 'role' => 'Frontend Developer', 'file' => 'lintang.webp'], ['name' => 'Rafi', 'role' => 'Backend Developer', 'file' => 'rafi.webp']] as $i => $member)
                                        <div class="flex flex-col items-center text-center {{ $i === 2 ? 'col-span-2 sm:col-span-1' : '' }}">
                                            <div class="h-24 w-24 overflow-hidden rounded-full bg-primary-50 ring-4 ring-primary-100 sm:h-28 sm:w-28">
                                                <img src="{{ asset('assets/images/teams/' . $member['file']) }}"
                                                    onerror="this.style.display='none';"
                                                    alt="{{ $member['name'] }}" loading="lazy" width="112" height="112"
                                                    class="h-full w-full object-cover">
                                            </div>
                                            <h4 class="mt-4 text-sm font-bold text-surface-900 sm:text-base">{{ $member['name'] }}</h4>
                                            <p class="mt-1 text-xs font-semibold text-primary-600 sm:text-sm">{{ $member['role'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-8 flex flex-row items-center justify-center gap-5 sm:gap-8">
                                    @foreach ([['name' => 'Jekky', 'role' => 'Web Designer', 'file' => 'jekky.webp'], ['name' => 'Affan', 'role' => 'Frontend Developer', 'file' => 'affan1.webp']] as $member)
                                        <div class="flex w-1/2 flex-col items-center text-center sm:w-1/3">
                                            <div class="h-24 w-24 overflow-hidden rounded-full bg-primary-50 ring-4 ring-primary-100 sm:h-28 sm:w-28">
                                                <img src="{{ asset('assets/images/teams/' . $member['file']) }}"
                                                    onerror="this.style.display='none';"
                                                    alt="{{ $member['name'] }}" loading="lazy" width="112" height="112"
                                                    class="h-full w-full object-cover">
                                            </div>
                                            <h4 class="mt-4 text-sm font-bold text-surface-900 sm:text-base">{{ $member['name'] }}</h4>
                                            <p class="mt-1 text-xs font-semibold text-primary-600 sm:text-sm">{{ $member['role'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <button id="back-to-top"
                type="button"
                aria-label="Kembali ke atas"
                class="fixed z-[95] w-12 h-12 rounded-full
                       bg-primary-500 hover:bg-primary-700 text-white
                       shadow-lg hover:shadow-xl
                       flex items-center justify-center
                       opacity-0 translate-y-4 pointer-events-none
                       transition-all duration-300
                       hover:-translate-y-1
                       left-5 bottom-24
                       lg:bottom-6">
            <i data-lucide="arrow-up" class="w-5 h-5"></i>
        </button>
    </div>{{-- end content wrapper --}}

        {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- MOBILE BOTTOM NAVIGATION — hanya tampil < lg --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
        $isHome = request()->routeIs('public.home');
        $isPrestasi = request()->routeIs('public.kesiswaan.prestasi.*');
    @endphp

        <nav x-data="{ drawerOpen: false }"
         class="lg:hidden fixed bottom-0 left-0 right-0 z-[90]
                bg-white/95 backdrop-blur-md
                border-t border-surface-200
                shadow-[0_-4px_20px_-4px_rgba(0,0,0,0.08)]"
         style="padding-bottom: env(safe-area-inset-bottom, 0px);"
         aria-label="Navigasi utama">

        <div class="grid grid-cols-5 items-center h-16 relative">

            {{-- 1. Beranda --}}
            <a href="{{ url('/') }}"
               class="flex flex-col items-center justify-center gap-1 h-full
                      {{ $isHome ? 'text-primary-600' : 'text-surface-400' }}
                      transition-colors duration-200">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-[10px] font-semibold">Beranda</span>
            </a>

            {{-- 2. Berita --}}
            <a href="{{ route('public.news.index') }}"
               class="flex flex-col items-center justify-center gap-1 h-full
                      {{ request()->routeIs('public.news.*') ? 'text-primary-600' : 'text-surface-400' }}
                      transition-colors duration-200">
                <i data-lucide="newspaper" class="w-5 h-5"></i>
                <span class="text-[10px] font-semibold">Berita</span>
            </a>

            {{-- 3. Chatbot (TENGAH) --}}
            <div class="relative flex justify-center h-full">
                <button type="button"
                        id="bottom-chatbot-btn"
                        @click="drawerOpen = false; window.dispatchEvent(new CustomEvent('open-chatbot'))"
                        aria-label="Buka Chatbot"
                        class="absolute -top-5 w-14 h-14 rounded-xl
                               bg-white
                               shadow-lg shadow-primary-500/40
                               ring-4 ring-white
                               flex items-center justify-center
                               hover:scale-105 active:scale-95
                               transition-transform duration-200 overflow-hidden p-0">
                    <img src="{{ asset('assets/images/chatbot/chatbot.webp') }}"
                         alt="Chatbot"
                         class="w-full h-full object-cover block scale-125"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <span class="w-full h-full hidden items-center justify-center bg-gradient-to-br from-primary-600 to-primary-800">
                        <i data-lucide="message-circle" class="w-6 h-6 text-white"></i>
                    </span>
                </button>
                <span class="absolute bottom-0 text-[10px] font-semibold text-surface-400">
                    Tanya
                </span>
            </div>

            {{-- 4. Prestasi --}}
            <a href="{{ route('public.kesiswaan.prestasi.index') }}"
               class="flex flex-col items-center justify-center gap-1 h-full
                      {{ $isPrestasi ? 'text-primary-600' : 'text-surface-400' }}
                      transition-colors duration-200">
                <i data-lucide="trophy" class="w-5 h-5"></i>
                <span class="text-[10px] font-semibold">Prestasi</span>
            </a>

            {{-- 5. Peta --}}
            <a href="{{ route('public.rooms.map') }}"
               class="flex flex-col items-center justify-center gap-1 h-full
                      {{ request()->routeIs('public.rooms.map') ? 'text-primary-600' : 'text-surface-400' }}
                      transition-colors duration-200">
                <i data-lucide="map-pin" class="w-5 h-5"></i>
                <span class="text-[10px] font-semibold">Peta</span>
            </a>
        </div>

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- DRAWER "LAINNYA" — slide dari kanan --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div x-show="drawerOpen"
             x-cloak
             class="fixed inset-0 z-[95] lg:hidden">

            {{-- Backdrop --}}
            <div @click="drawerOpen = false"
                 x-show="drawerOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="absolute inset-0 bg-black/50 backdrop-blur-sm">
            </div>

            {{-- Panel --}}
            <div x-show="drawerOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="absolute right-0 top-0 bottom-0 w-[85%] max-w-sm
                        bg-white shadow-2xl overflow-y-auto">

                {{-- Header --}}
                <div class="sticky top-0 z-10 bg-gradient-to-br from-primary-600 to-primary-800
                            text-white px-5 py-5 pb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            @php $drawerLogo = \App\Models\SchoolProfile::current()?->logo; @endphp
                            @if ($drawerLogo)
                                <img src="{{ asset('storage/' . $drawerLogo) }}"
                                     class="h-10 w-auto object-contain">
                            @else
                                <img src="{{ asset('assets/images/logo/logo.webp') }}"
                                     class="h-10 w-auto object-contain">
                            @endif
                            <div>
                                <p class="text-sm font-bold">SMK Negeri 2</p>
                                <p class="text-[10px] text-white/70 tracking-wider">KARANGANYAR</p>
                            </div>
                        </div>
                        <button type="button"
                                @click="drawerOpen = false"
                                aria-label="Tutup menu"
                                class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20
                                       flex items-center justify-center transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- Menu list --}}
                <div class="p-4 space-y-5">

                    {{-- Tentang --}}
                    <div>
                        <p class="text-[11px] font-bold text-primary-600 tracking-widest uppercase mb-2 px-1">
                            Tentang
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('public.profile.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="info" class="w-4 h-4 text-primary-600"></i>
                                Identitas Sekolah
                            </a>
                            <a href="{{ route('public.profile.sambutan') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="message-square" class="w-4 h-4 text-primary-600"></i>
                                Sambutan Kepala Sekolah
                            </a>
                            <a href="{{ route('public.profile.sejarah') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="history" class="w-4 h-4 text-primary-600"></i>
                                Sejarah Sekolah
                            </a>
                            <a href="{{ route('public.profile.struktur') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="network" class="w-4 h-4 text-primary-600"></i>
                                Struktur Organisasi
                            </a>
                        </div>
                    </div>

                    {{-- Sarpras --}}
                    <div>
                        <p class="text-[11px] font-bold text-primary-600 tracking-widest uppercase mb-2 px-1">
                            Sarpras
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('public.rooms.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="door-open" class="w-4 h-4 text-primary-600"></i>
                                Daftar Ruangan
                            </a>
                            <a href="{{ route('public.rooms.map') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="map-pin" class="w-4 h-4 text-primary-600"></i>
                                Peta Sekolah
                            </a>
                            <a href="{{ route('public.sarpras.pengaduan') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-primary-600"></i>
                                Buat Laporan
                            </a>
                        </div>
                    </div>

                    {{-- Akademik --}}
                    <div>
                        <p class="text-[11px] font-bold text-primary-600 tracking-widest uppercase mb-2 px-1">
                            Akademik
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('public.akademik.guru.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="users" class="w-4 h-4 text-primary-600"></i>
                                Guru & Tenaga Kependidikan
                            </a>
                            <a href="{{ route('public.akademik.kompetensi.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="book-open" class="w-4 h-4 text-primary-600"></i>
                                Kompetensi Keahlian
                            </a>
                            <a href="{{ route('public.akademik.spmb.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="clipboard-check" class="w-4 h-4 text-primary-600"></i>
                                SPMB
                            </a>
                            <a href="{{ route('public.akademik.produk.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="package" class="w-4 h-4 text-primary-600"></i>
                                Produk Unggulan
                            </a>
                            <a href="{{ route('public.akademik.alumni.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="graduation-cap" class="w-4 h-4 text-primary-600"></i>
                                Alumni
                            </a>
                        </div>
                    </div>

                    {{-- Kesiswaan --}}
                    <div>
                        <p class="text-[11px] font-bold text-primary-600 tracking-widest uppercase mb-2 px-1">
                            Kesiswaan
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="sparkles" class="w-4 h-4 text-primary-600"></i>
                                Ekstrakurikuler
                            </a>
                            <a href="{{ route('public.kesiswaan.tata-tertib.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="file-text" class="w-4 h-4 text-primary-600"></i>
                                Tata Tertib
                            </a>
                            <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-surface-700
                                      hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                <i data-lucide="briefcase" class="w-4 h-4 text-primary-600"></i>
                                BKK & PKL
                            </a>
                        </div>
                    </div>

                    {{-- Berita --}}
                    <a href="{{ route('public.news.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-surface-900
                              bg-surface-50 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                        <i data-lucide="newspaper" class="w-4 h-4 text-primary-600"></i>
                        Berita
                    </a>

                    {{-- CTA --}}
                    <div class="pt-3 border-t border-surface-200 space-y-2">
                        @guest
                            <a href="{{ route('public.akademik.spmb.index') }}"
                               class="flex items-center justify-center gap-2 w-full py-3
                                      bg-gradient-to-br from-primary-600 to-primary-800
                                      text-white text-sm font-semibold rounded-xl
                                      shadow-md hover:shadow-lg transition-all">
                                <i data-lucide="clipboard-check" class="w-4 h-4"></i>
                                Daftar SPMB
                            </a>
                        @endguest

                        @auth
                            @php
                                $drawerDashboardRoute = match (auth()->user()->role->value) {
                                    'admin' => 'admin.dashboard',
                                    'waka_sarpras' => 'waka.dashboard',
                                    'kepala_sekolah' => 'kepala.dashboard',
                                    'pengguna_ruangan' => 'pengguna.dashboard',
                                    default => 'public.home',
                                };
                            @endphp
                            <a href="{{ route($drawerDashboardRoute) }}"
                               class="flex items-center justify-center gap-2 w-full py-3
                                      bg-gradient-to-br from-primary-600 to-primary-800
                                      text-white text-sm font-semibold rounded-xl
                                      shadow-md hover:shadow-lg transition-all">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center justify-center gap-2 w-full py-3
                                               border border-red-200 text-red-600 text-sm font-semibold rounded-xl
                                               hover:bg-red-50 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <x-chatbot />
    @stack('scripts')
</body>

</html>