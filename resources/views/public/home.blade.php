@extends('layouts.public')

@section('title', 'Beranda')
@section('description', 'Website resmi ' . ($school?->school_name ?? 'SMK Negeri 2 Karanganyar'))

@section('content')

<main class="relative overflow-hidden">

    {{-- ============ BACKGROUND UTAMA (KONTINU) ============ --}}
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-b from-primary-50/50 via-white to-accent-amber/5"></div>
        <div class="absolute inset-0 opacity-[0.15]"
            style="background-image: radial-gradient(circle at 1px 1px, rgb(108 74 182 / 0.15) 1px, transparent 0);
                    background-size: 40px 40px;"></div>
        <div class="absolute -top-40 -left-40 w-[800px] h-[800px] rounded-full
                    bg-gradient-to-br from-primary-200/35 via-primary-100/20 to-transparent blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-[700px] h-[700px] rounded-full
                    bg-gradient-to-tl from-accent-amber/20 via-primary-200/15 to-transparent blur-3xl"></div>
        <div class="absolute top-2/3 -left-40 w-[700px] h-[700px] rounded-full
                    bg-gradient-to-br from-primary-200/25 to-transparent blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-[800px] h-[800px] rounded-full
                    bg-gradient-to-tl from-accent-amber/20 via-primary-100/15 to-transparent blur-3xl"></div>
    </div>

    {{-- ============================================================ --}}
    {{-- HERO SECTION --}}
    {{-- ============================================================ --}}
    <section class="relative min-h-[calc(100vh-80px)] flex items-center overflow-hidden">

                {{-- ============ PARTIKEL DEKORATIF — RAMAI & RESPONSIF ============ --}}
        {{-- Belah ketupat besar (outline) --}}
        <div class="absolute top-12 left-4 sm:top-16 sm:left-10 w-10 h-10 sm:w-16 sm:h-16 rotate-45 border-2 border-primary-400/20 pointer-events-none"></div>
        <div class="absolute bottom-40 right-6 sm:bottom-32 sm:right-12 w-14 h-14 sm:w-24 sm:h-24 rotate-45 border border-primary-500/20 pointer-events-none"></div>
        <div class="absolute top-[55%] right-4 sm:right-12 w-8 h-8 sm:w-14 sm:h-14 rotate-45 border-2 border-accent-amber/25 pointer-events-none"></div>

        {{-- Belah ketupat solid kecil --}}
        <div class="absolute top-1/3 left-2 sm:-left-2 w-3 h-3 sm:w-4 sm:h-4 rotate-45 bg-accent-amber/60 pointer-events-none"></div>
        <div class="absolute top-20 right-6 sm:top-24 sm:right-16 w-5 h-5 sm:w-8 sm:h-8 rotate-45 bg-accent-amber/40 pointer-events-none"></div>
        <div class="absolute bottom-48 left-8 sm:bottom-40 sm:left-16 w-2 h-2 sm:w-3 sm:h-3 rotate-45 bg-primary-500/50 pointer-events-none"></div>
        <div class="absolute top-[40%] left-[15%] w-3 h-3 sm:w-5 sm:h-5 rotate-45 bg-primary-400/40 pointer-events-none"></div>
        <div class="absolute top-[75%] left-[35%] w-2 h-2 sm:w-4 sm:h-4 rotate-45 bg-accent-amber/50 pointer-events-none"></div>
        <div class="absolute top-[15%] right-[25%] w-3 h-3 sm:w-5 sm:h-5 rotate-45 bg-primary-500/40 pointer-events-none"></div>
        <div class="absolute bottom-[20%] right-[30%] w-4 h-4 sm:w-6 sm:h-6 rotate-45 bg-accent-amber/30 pointer-events-none"></div>

        {{-- Lingkaran outline --}}
        <div class="absolute top-[18%] left-[40%] w-6 h-6 sm:w-10 sm:h-10 rounded-full border-2 border-primary-400/25 pointer-events-none"></div>
        <div class="absolute top-[60%] right-[35%] w-4 h-4 sm:w-8 sm:h-8 rounded-full border border-accent-amber/30 pointer-events-none"></div>
        <div class="absolute bottom-[15%] left-[45%] w-3 h-3 sm:w-6 sm:h-6 rounded-full border-2 border-primary-500/20 pointer-events-none"></div>

        {{-- Lingkaran isi --}}
        <div class="absolute top-[30%] right-[10%] w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-accent-amber/60 pointer-events-none"></div>
        <div class="absolute top-[70%] left-[20%] w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-primary-500/60 pointer-events-none"></div>
        <div class="absolute top-[12%] left-[25%] w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-primary-400/50 pointer-events-none"></div>
        <div class="absolute bottom-[30%] right-[15%] w-1.5 h-1.5 sm:w-2.5 sm:h-2.5 rounded-full bg-accent-amber/50 pointer-events-none"></div>

        {{-- Plus signs --}}
        <div class="absolute top-[22%] left-[8%] w-3 h-3 sm:w-4 sm:h-4 text-primary-400/40 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </div>
        <div class="absolute top-[45%] right-[8%] w-2.5 h-2.5 sm:w-3.5 sm:h-3.5 text-accent-amber/50 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </div>
        <div class="absolute bottom-[25%] left-[10%] w-3 h-3 sm:w-4 sm:h-4 text-primary-500/40 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </div>
        <div class="absolute top-[80%] right-[22%] w-2.5 h-2.5 sm:w-3 sm:h-3 text-accent-amber/40 pointer-events-none">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
        </div>

        {{-- Garis dekoratif tipis --}}
        <div class="absolute top-[35%] left-[5%] w-12 h-px sm:w-20 bg-gradient-to-r from-primary-400/40 to-transparent pointer-events-none"></div>
        <div class="absolute bottom-[40%] right-[5%] w-10 h-px sm:w-16 bg-gradient-to-l from-accent-amber/40 to-transparent pointer-events-none"></div>
        <div class="absolute top-[8%] left-[60%] w-8 h-px sm:w-14 bg-gradient-to-r from-primary-500/30 to-transparent pointer-events-none"></div>

        {{-- Blob blur kecil (glow) --}}
        <div class="absolute top-[28%] left-[18%] w-16 h-16 sm:w-24 sm:h-24 rounded-full bg-primary-300/20 blur-2xl pointer-events-none"></div>
        <div class="absolute bottom-[18%] right-[12%] w-20 h-20 sm:w-32 sm:h-32 rounded-full bg-accent-amber/15 blur-3xl pointer-events-none"></div>
        <div class="absolute top-[65%] left-[50%] w-14 h-14 sm:w-20 sm:h-20 rounded-full bg-primary-200/25 blur-2xl pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-6xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-8 md:gap-12">

                {{-- LEFT --}}
                <div class="animate-fade-in-up">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight text-surface-900 tracking-tight">
                        SMK Negeri 2<br>
                        <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                            Karanganyar
                        </span>
                    </h1>

                    <p class="mt-5 max-w-md text-sm md:text-base leading-relaxed text-surface-500">
                        {{ $school?->description ?? 'Mewujudkan pelajar yang berkarakter, memiliki sopan santun, dan lulusan siap kerja.' }}
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row flex-wrap gap-3">
                        <a href="{{ route('public.profile.sejarah') }}"
                            class="group inline-flex items-center justify-center gap-4 px-6 py-4 rounded-2xl
                            bg-gradient-to-br from-primary-600 to-primary-800
                            hover:shadow-xl hover:shadow-primary-500/30
                            transition-all duration-300 hover:-translate-y-1 text-white">
                            <div class="text-left">
                                <p class="text-sm font-semibold leading-tight">Sejarah Berdirinya</p>
                                <p class="text-xs opacity-80 mt-0.5">SMKN 2 KARANGANYAR</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center
                                        group-hover:scale-110 transition-transform">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                        </a>

                        <a href="{{ route('public.akademik.spmb.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl
                            border-2 border-surface-200 hover:border-primary-400
                            text-surface-700 hover:text-primary-700
                            text-sm font-semibold transition-all duration-200
                            hover:-translate-y-1 hover:shadow-lg bg-white/70 backdrop-blur-sm">
                            Info SPMB
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                {{-- RIGHT — Foto 3 Siswa (center di mobile) --}}
                <div class="flex justify-center md:justify-end animate-fade-in-up stagger-2 md:mt-4 lg:mt-8">
                    <div class="relative w-full max-w-[300px] sm:max-w-[400px] md:max-w-[520px] pb-16 md:pb-24 mx-auto md:mx-0">

                        {{-- Foto PNG transparan — fade-out bottom --}}
                        <img
                            src="{{ asset('assets/images/hero-siswa.png') }}"
                            alt="Siswa SMK Negeri 2 Karanganyar"
                            loading="eager"
                            class="relative w-full h-auto block mx-auto
                                   [mask-image:linear-gradient(to_bottom,black_0%,black_70%,transparent_100%)]
                                   [-webkit-mask-image:linear-gradient(to_bottom,black_0%,black_70%,transparent_100%)]"
                        >
                    </div>
                </div>
            </div>
        </div>

        {{-- WAVE DIVIDER --}}
        <div class="absolute bottom-0 left-0 right-0 pointer-events-none overflow-hidden leading-none" aria-hidden="true">
            <div class="relative w-full h-[100px] md:h-[160px]">
                <svg class="absolute inset-0 w-[200%] h-full wave-slide-slow" viewBox="0 0 2880 160"
                    preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0,90 C180,55 360,110 540,90 C720,70 900,115 1080,90 C1260,65 1440,110 1620,90 C1800,70 1980,115 2160,90 C2340,65 2520,110 2700,90 C2820,75 2880,90 2880,90 L2880,160 L0,160 Z"
                        fill="rgb(196 181 253 / 0.35)" />
                    <path
                        d="M2880,90 C3060,55 3240,110 3420,90 C3600,70 3780,115 3960,90 C4140,65 4320,110 4500,90 C4680,70 4860,115 5040,90 C5220,65 5400,110 5580,90 C5700,75 5760,90 5760,90 L5760,160 L2880,160 Z"
                        fill="rgb(196 181 253 / 0.35)" />
                </svg>
                <svg class="absolute inset-0 w-[200%] h-full wave-slide-medium" viewBox="0 0 2880 160"
                    preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M0,110 C220,80 440,135 660,110 C880,85 1100,130 1320,110 C1540,90 1760,135 1980,110 C2200,85 2420,130 2640,110 C2820,90 2880,105 2880,110 L2880,160 L0,160 Z"
                        fill="rgb(252 211 77 / 0.30)" />
                    <path
                        d="M2880,110 C3100,80 3320,135 3540,110 C3760,85 3980,130 4200,110 C4420,90 4640,135 4860,110 C5080,85 5300,130 5520,110 C5700,90 5760,105 5760,110 L5760,160 L2880,160 Z"
                        fill="rgb(252 211 77 / 0.30)" />
                </svg>
                <svg class="absolute inset-0 w-[200%] h-full wave-slide-fast" viewBox="0 0 2880 160"
                    preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="wave-front-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="rgb(196 181 253 / 0.25)" />
                            <stop offset="100%" stop-color="rgb(196 181 253 / 0.05)" />
                        </linearGradient>
                    </defs>
                    <path
                        d="M0,130 C260,105 520,150 780,130 C1040,110 1300,155 1560,130 C1820,105 2080,150 2340,130 C2600,110 2860,150 2880,130 L2880,160 L0,160 Z"
                        fill="url(#wave-front-grad)" />
                    <path
                        d="M2880,130 C3140,105 3400,150 3660,130 C3920,110 4180,155 4440,130 C4700,105 4960,150 5220,130 C5480,110 5740,150 5760,130 L5760,160 L2880,160 Z"
                        fill="url(#wave-front-grad)" />
                </svg>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- KENAPA HARUS SEKOLAH DI SINI? — SWIPER (mobile) + GRID (desktop) --}}
    {{-- ============================================================ --}}
    <section class="relative py-16 md:py-24 overflow-hidden bg-surface-50">

        <div class="absolute inset-0 pointer-events-none opacity-[0.03]"
            style="background-image: radial-gradient(circle, #000 1px, transparent 1px); background-size: 24px 24px;">
        </div>

        <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-gradient-to-b from-primary-100/50 to-transparent blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 w-full">

            {{-- Header --}}
            <div class="text-center mb-10 md:mb-14">
                <span class="inline-block text-[11px] font-bold text-primary-600 tracking-[0.2em] uppercase mb-3">
                    Keunggulan Kami
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-surface-900 tracking-tight">
                    Kenapa Harus <span class="text-primary-600">Sekolah di Sini?</span>
                </h2>
                <p class="mt-3 text-sm md:text-base text-surface-500 max-w-xl mx-auto leading-relaxed">
                    Alasan mengapa SMK Negeri 2 Karanganyar menjadi pilihan tepat untuk masa depan.
                </p>
            </div>

            @php
                $reasons = [
                    [
                        'icon' => 'trophy',
                        'title' => 'Berprestasi',
                        'desc' => 'Siswa kami rutin menorehkan prestasi di tingkat kabupaten, provinsi, nasional, bahkan internasional.',
                        'route' => route('public.kesiswaan.prestasi.index'),
                        'color' => '#f59e0b',
                        'bg-light' => '#FEF3C7',
                    ],
                    [
                        'icon' => 'book-open',
                        'title' => 'Akademik Berkualitas',
                        'desc' => 'Kurikulum berbasis industri dengan pengajar berpengalaman dan bersertifikat.',
                        'route' => route('public.akademik.kompetensi.index'),
                        'color' => '#6C4AB6',
                        'bg-light' => '#F3EFFB',
                    ],
                    [
                        'icon' => 'briefcase',
                        'title' => 'Siap Kerja',
                        'desc' => 'Kerja sama dengan berbagai industri untuk PKL, BKK, dan penyaluran kerja lulusan.',
                        'route' => route('public.kesiswaan.pkl-bkk.index'),
                        'color' => '#0EA5E9',
                        'bg-light' => '#E5F6FD',
                    ],
                    [
                        'icon' => 'sparkles',
                        'title' => 'Ekstrakurikuler',
                        'desc' => 'Beragam ekskul untuk mengasah minat, bakat, dan soft skill di luar akademik.',
                        'route' => route('public.kesiswaan.ekstrakurikuler.index'),
                        'color' => '#EF4444',
                        'bg-light' => '#FBEBEB',
                    ],
                    [
                        'icon' => 'building-2',
                        'title' => 'Fasilitas Lengkap',
                        'desc' => 'Lab komputer, bengkel praktik, perpustakaan, masjid, dan sarana olahraga.',
                        'route' => route('public.rooms.map'),
                        'color' => '#10B981',
                        'bg-light' => '#E7F6EC',
                    ],
                    [
                        'icon' => 'compass',
                        'title' => 'Panduan SPMB',
                        'desc' => 'Informasi lengkap penerimaan siswa baru: jalur, syarat, jadwal, dan tata cara pendaftaran.',
                        'route' => route('public.akademik.spmb.panduan'),
                        'color' => '#F97316',
                        'bg-light' => '#FDF0E7',
                    ],
                ];
            @endphp

            {{-- Mobile: Swiper --}}
            <div class="md:hidden">
                <div class="swiper reasons-swiper !overflow-visible !pb-12">
                    <div class="swiper-wrapper">
                        @foreach ($reasons as $r)
                            <div class="swiper-slide !h-auto">
                                <a href="{{ $r['route'] }}"
                                    class="group relative flex flex-col h-full bg-white rounded-2xl p-6
                                    border border-surface-200/80 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]
                                    overflow-hidden">

                                    <div class="absolute top-0 left-0 w-full h-1"
                                        style="background-color: {{ $r['color'] }};"></div>

                                    <div class="relative w-12 h-12 flex items-center justify-center rounded-xl mb-5"
                                        style="background: {{ $r['bg-light'] }};">
                                        <i data-lucide="{{ $r['icon'] }}" class="w-6 h-6"
                                            style="color: {{ $r['color'] }};"></i>
                                    </div>

                                    <h3 class="relative text-lg font-bold text-surface-900 leading-tight mb-2">
                                        {{ $r['title'] }}
                                    </h3>
                                    <p class="relative text-sm text-surface-500 leading-relaxed mb-6 flex-1">
                                        {{ $r['desc'] }}
                                    </p>

                                    <div class="relative mt-auto flex items-center gap-3 text-sm font-bold"
                                        style="color: {{ $r['color'] }};">
                                        <span>Selengkapnya</span>
                                        <div class="flex-1 h-px bg-current opacity-20"></div>
                                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-pagination reasons-pagination !-bottom-2"></div>
                </div>
            </div>

            {{-- Desktop: Grid --}}
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($reasons as $r)
                    <a href="{{ $r['route'] }}"
                        class="group relative flex flex-col h-full bg-white rounded-2xl p-8
                        border border-surface-200/80 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]
                        transition-all duration-500 hover:shadow-[0_20px_40px_-10px_rgba(0,0,0,0.08)]
                        hover:-translate-y-1 overflow-hidden">

                        <div class="absolute top-0 left-0 w-full h-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                            style="background-color: {{ $r['color'] }};"></div>

                        <div class="relative w-12 h-12 flex items-center justify-center rounded-xl mb-6
                                transition-transform duration-500 group-hover:scale-110"
                            style="background: {{ $r['bg-light'] }};">
                            <i data-lucide="{{ $r['icon'] }}" class="w-6 h-6"
                                style="color: {{ $r['color'] }};"></i>
                        </div>

                        <h3 class="relative text-xl font-bold text-surface-900 leading-tight mb-3
                            group-hover:text-primary-700 transition-colors">
                            {{ $r['title'] }}
                        </h3>
                        <p class="relative text-sm text-surface-500 leading-relaxed mb-8 flex-1">
                            {{ $r['desc'] }}
                        </p>

                        <div class="relative mt-auto flex items-center gap-3 text-sm font-bold
                                transition-all duration-300"
                            style="color: {{ $r['color'] }};">
                            <span>Selengkapnya</span>
                            <div class="flex-1 h-px bg-current opacity-20 transition-all duration-500 group-hover:opacity-100"></div>
                            <i data-lucide="arrow-right"
                                class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- KOMPETENSI KEAHLIAN — SWIPER (mobile) + GRID (desktop) --}}
    {{-- ============================================================ --}}
    <section class="relative py-12 md:py-16 overflow-hidden">
        <svg class="absolute -top-72 -left-72 -z-10 hidden h-[900px] w-[900px] pointer-events-none lg:block"
            viewBox="0 0 900 900" fill="none" aria-hidden="true">
            <circle cx="450" cy="450" r="400" stroke="#6C4AB6" stroke-width="1.5"
                stroke-dasharray="10 12" opacity="0.18" />
        </svg>
        <svg class="absolute -bottom-72 -right-72 -z-10 hidden h-[900px] w-[900px] pointer-events-none lg:block"
            viewBox="0 0 900 900" fill="none" aria-hidden="true">
            <circle cx="450" cy="450" r="400" stroke="#f59e0b" stroke-width="1.5"
                stroke-dasharray="10 12" opacity="0.18" />
        </svg>

        <div class="relative z-10 max-w-6xl mx-auto px-4">
            <div class="text-center mb-10 md:mb-14 scroll-reveal">
                <span class="inline-block text-xs font-semibold text-primary-600 tracking-widest uppercase mb-2">
                    Program Studi
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-surface-900 tracking-tight">
                    Kompetensi <span class="text-primary-600">Keahlian</span>
                </h2>
                <p class="mt-3 text-sm text-surface-500 max-w-md mx-auto px-4">
                    Program keahlian unggulan yang siap mencetak lulusan kompeten.
                </p>
            </div>

            @php
                $palette = [
                    ['bg' => '#35B768', 'light' => '#E7F6EC', 'icon' => 'calculator'],
                    ['bg' => '#3589BD', 'light' => '#E7F1F8', 'icon' => 'network'],
                    ['bg' => '#CF4141', 'light' => '#FBEBEB', 'icon' => 'briefcase'],
                    ['bg' => '#EA580C', 'light' => '#FDF0E7', 'icon' => 'wrench'],
                    ['bg' => '#0EA5E9', 'light' => '#E5F6FD', 'icon' => 'server'],
                    ['bg' => '#7C3AED', 'light' => '#F1EAFE', 'icon' => 'cpu'],
                    ['bg' => '#6C4AB6', 'light' => '#F3EFFB', 'icon' => 'code'],
                    ['bg' => '#D97706', 'light' => '#FEF4E7', 'icon' => 'palette'],
                ];
            @endphp

            {{-- Mobile: Swiper --}}
            <div class="md:hidden">
                <div class="swiper departments-swiper !overflow-visible !pb-12">
                    <div class="swiper-wrapper">
                        @forelse ($departments as $dept)
                            @php $c = $palette[$loop->index % count($palette)]; @endphp
                            <div class="swiper-slide !h-auto">
                                <a href="{{ route('public.akademik.kompetensi.show', $dept) }}"
                                    class="group relative flex flex-col h-full">

                                    <div class="relative h-[150px] flex items-end justify-center z-10 px-4">
                                        @if ($dept->image)
                                            <img src="{{ asset('storage/' . $dept->image) }}" alt="{{ $dept->name }}"
                                                loading="lazy" width="160" height="160"
                                                class="max-h-[140px] max-w-full object-contain drop-shadow-xl">
                                        @else
                                            <div class="w-[110px] h-[110px] rounded-3xl flex items-center justify-center shadow-lg"
                                                style="background: linear-gradient(135deg, {{ $c['light'] }}, {{ $c['bg'] }}20);">
                                                <i data-lucide="{{ $c['icon'] }}" class="w-14 h-14"
                                                    style="color: {{ $c['bg'] }};"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="relative rounded-3xl -mt-8 pt-12 pb-6 px-5 flex-1 flex flex-col
                                        text-white overflow-hidden"
                                        style="background: linear-gradient(135deg, {{ $c['bg'] }}, {{ $c['bg'] }}dd);">

                                        <div class="absolute inset-0 rounded-3xl opacity-[0.08] pointer-events-none"
                                            style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 20px 20px;"></div>

                                        <div class="relative flex-1 flex flex-col">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                bg-white/20 backdrop-blur-sm text-[10px] font-bold uppercase tracking-widest mb-3 self-start">
                                                {{ $dept->code }}
                                            </div>

                                            <h3 class="text-base font-bold leading-tight line-clamp-2">
                                                {{ $dept->name }}</h3>

                                            <p class="text-xs leading-relaxed mt-3 opacity-90 line-clamp-3">
                                                {{ $dept->description }}
                                            </p>

                                            <div class="mt-auto pt-5 inline-flex items-center gap-1.5 text-xs font-bold
                                                underline decoration-white/40">
                                                Selengkapnya
                                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="swiper-slide text-center py-12 text-surface-400">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                <p>Belum ada data jurusan.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination departments-pagination !-bottom-2"></div>
                </div>
            </div>

            {{-- Desktop: Grid --}}
            <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-5">
                @forelse ($departments as $dept)
                    @php $c = $palette[$loop->index % count($palette)]; @endphp

                    <a href="{{ route('public.akademik.kompetensi.show', $dept) }}"
                        class="group relative flex flex-col h-full">

                        <div class="relative h-[170px] flex items-end justify-center z-10 px-4">
                            @if ($dept->image)
                                <img src="{{ asset('storage/' . $dept->image) }}" alt="{{ $dept->name }}"
                                    loading="lazy" width="160" height="160"
                                    class="max-h-[160px] max-w-full object-contain drop-shadow-xl
                                    transition-all duration-500 ease-smooth
                                    group-hover:-translate-y-3 group-hover:scale-105">
                            @else
                                <div class="w-[130px] h-[130px] rounded-3xl flex items-center justify-center
                                    shadow-lg transition-all duration-500 ease-smooth
                                    group-hover:-translate-y-3 group-hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $c['light'] }}, {{ $c['bg'] }}20);">
                                    <i data-lucide="{{ $c['icon'] }}" class="w-16 h-16"
                                        style="color: {{ $c['bg'] }};"></i>
                                </div>
                            @endif
                        </div>

                        <div class="relative rounded-3xl -mt-8 pt-14 pb-6 px-5 flex-1 flex flex-col
                            text-white transition-all duration-500 ease-smooth
                            group-hover:shadow-2xl group-hover:-translate-y-1 overflow-hidden"
                            style="background: linear-gradient(135deg, {{ $c['bg'] }}, {{ $c['bg'] }}dd);">

                            <div class="absolute inset-0 rounded-3xl opacity-[0.08] pointer-events-none"
                                style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 20px 20px;">
                            </div>

                            <div class="relative flex-1 flex flex-col">
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                    bg-white/20 backdrop-blur-sm text-[10px] font-bold uppercase tracking-widest mb-3 self-start">
                                    {{ $dept->code }}
                                </div>

                                <h3 class="text-lg font-bold leading-tight line-clamp-2">
                                    {{ $dept->name }}</h3>

                                <p class="text-xs leading-relaxed mt-3 opacity-90 line-clamp-3">
                                    {{ $dept->description }}
                                </p>

                                <div class="mt-auto pt-5 inline-flex items-center gap-1.5 text-xs font-bold
                                    underline decoration-white/40 hover:decoration-white transition-all duration-300">
                                    Selengkapnya
                                    <i data-lucide="arrow-right"
                                        class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12 text-surface-400">
                        <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                        <p>Belum ada data jurusan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- LULUSAN TERBAIK --}}
    {{-- ============================================================ --}}
    @if ($alumni->isNotEmpty())
        @php
            $featuredAlumni = $alumni->first();
            $otherAlumni    = $alumni->skip(1)->take(4);
        @endphp

        <section class="relative mt-12 pb-20 overflow-hidden">
            <div class="absolute top-16 right-12 w-12 h-12 rotate-45 border-2 border-primary-400/20 pointer-events-none hidden lg:block"></div>
            <div class="absolute top-1/2 left-6 w-3 h-3 rotate-45 bg-accent-amber/50 pointer-events-none hidden md:block shadow-md shadow-accent-amber/40"></div>
            <div class="absolute bottom-20 right-8 w-5 h-5 rotate-45 bg-primary-500/40 pointer-events-none hidden lg:block shadow-lg shadow-primary-500/20"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-4">
                <div class="relative text-center mb-14 scroll-reveal">
                    <span class="inline-block text-xs font-semibold text-primary-600 tracking-widest uppercase mb-2">
                        Jejak Sukses
                    </span>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-surface-900 tracking-tight">
                        Lulusan <span class="text-primary-600">Terbaik</span>
                    </h2>
                    <p class="mt-3 text-sm text-surface-500 max-w-md mx-auto">
                        Apresiasi untuk alumni berprestasi yang mengharumkan nama sekolah.
                    </p>
                </div>

                {{-- Desktop bento --}}
                <div class="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @if ($featuredAlumni)
                        <a href="{{ route('public.akademik.alumni.show', $featuredAlumni->slug) }}"
                            class="achievement-card group relative md:col-span-2 md:row-span-2
                                    bg-gradient-to-br from-primary-600 to-primary-800
                                    rounded-3xl overflow-hidden text-white
                                    flex flex-col p-8 lg:p-10 min-h-[420px]">
                            <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/10 pointer-events-none
                                        group-hover:scale-110 transition-transform duration-700"></div>
                            <div class="absolute -bottom-16 -left-16 w-48 h-48 rounded-full bg-white/5 pointer-events-none"></div>

                            <div class="relative inline-flex items-center gap-2 self-start
                                        px-3 py-1.5 rounded-full bg-accent-amber text-surface-900
                                        text-[10px] font-bold uppercase tracking-wider">
                                <i data-lucide="crown" class="w-3 h-3"></i>
                                Featured Alumni
                            </div>

                            <div class="relative mt-auto mb-6">
                                <div class="w-24 h-24 lg:w-32 lg:h-32 rounded-3xl overflow-hidden
                                            ring-4 ring-white/30 shadow-2xl bg-white/20
                                            transition-transform duration-500 group-hover:scale-105 group-hover:rotate-2">
                                    @if ($featuredAlumni->photo)
                                        <img src="{{ asset('storage/' . $featuredAlumni->photo) }}"
                                            alt="{{ $featuredAlumni->name }}"
                                            loading="lazy" width="128" height="128"
                                            class="w-full h-full object-cover object-[center_25%]">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i data-lucide="user" class="w-12 h-12 text-white/70"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <h3 class="relative text-2xl lg:text-3xl font-bold leading-tight">
                                {{ $featuredAlumni->name }}
                            </h3>

                            <div class="relative mt-3 flex flex-wrap items-center gap-3 text-xs">
                                @if ($featuredAlumni->graduation_year)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $featuredAlumni->graduation_year }}
                                    </span>
                                @endif
                                @if ($featuredAlumni->department)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-white/20">
                                        <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                        {{ $featuredAlumni->department }}
                                    </span>
                                @endif
                            </div>

                            @if ($featuredAlumni->achievement)
                                <div class="relative mt-4 flex items-start gap-2 text-sm text-white/90">
                                    <i data-lucide="trophy" class="w-4 h-4 text-accent-amber shrink-0 mt-0.5"></i>
                                    <span class="line-clamp-2">{{ $featuredAlumni->achievement }}</span>
                                </div>
                            @endif

                            <div class="relative mt-6 inline-flex items-center gap-2 text-sm font-semibold
                                        text-white group-hover:gap-3 transition-all">
                                Lihat Profil Lengkap
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </div>
                        </a>
                    @endif

                    @foreach ($otherAlumni as $i => $item)
                        <a href="{{ route('public.akademik.alumni.show', $item->slug) }}"
                            class="achievement-card group relative
                                    bg-white/80 backdrop-blur-sm rounded-3xl overflow-hidden
                                    border border-surface-200/70 flex flex-col p-5
                                    transition-all duration-500 hover:-translate-y-1">
                            <div class="relative flex justify-center mb-4">
                                <div class="relative w-20 h-20 rounded-2xl overflow-hidden
                                            bg-primary-50 ring-4 ring-white shadow-md">
                                    @if ($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}"
                                            alt="{{ $item->name }}"
                                            loading="lazy" width="80" height="80"
                                            class="w-full h-full object-cover object-[center_25%]">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                            <i data-lucide="user" class="w-8 h-8 text-primary-600"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="relative text-center flex-1 flex flex-col">
                                <h3 class="text-sm font-bold text-surface-900 leading-snug line-clamp-2">
                                    {{ $item->name }}
                                </h3>
                                <div class="mt-2 flex items-center justify-center gap-1.5 flex-wrap text-[10px]">
                                    @if ($item->graduation_year)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                    bg-surface-100 text-surface-600 font-medium">
                                            {{ $item->graduation_year }}
                                        </span>
                                    @endif
                                    @if ($item->department)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                    bg-primary-50 text-primary-700 font-medium">
                                            {{ $item->department }}
                                        </span>
                                    @endif
                                </div>
                                @if ($item->achievement)
                                    <div class="mt-3 inline-flex items-center gap-1 self-center
                                                px-2 py-0.5 rounded-full bg-accent-amber/15 text-accent-amber
                                                text-[10px] font-semibold max-w-full">
                                        <i data-lucide="trophy" class="w-2.5 h-2.5 shrink-0"></i>
                                        <span class="line-clamp-1">{{ $item->achievement }}</span>
                                    </div>
                                @endif
                                <div class="mt-auto pt-4">
                                    <div class="mt-3 inline-flex items-center justify-center gap-1 text-[11px] font-semibold
                                                text-primary-600 group-hover:text-primary-700">
                                        Lihat Profil
                                        <i data-lucide="arrow-right" class="w-3 h-3 transition-transform duration-300 group-hover:translate-x-1"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Mobile swiper --}}
                <div class="md:hidden">
                    <div class="swiper alumni-swiper !overflow-visible !pb-12">
                        <div class="swiper-wrapper">
                            @foreach ($alumni->take(6) as $item)
                                <div class="swiper-slide !h-auto">
                                    <a href="{{ route('public.akademik.alumni.show', $item->slug) }}"
                                    class="group block bg-white rounded-3xl overflow-hidden
                                            border border-surface-200 h-full flex flex-col">
                                        <div class="relative aspect-[4/3] bg-surface-100 overflow-hidden">
                                            @if ($item->photo)
                                                <img src="{{ asset('storage/' . $item->photo) }}"
                                                    alt="{{ $item->name }}"
                                                    loading="lazy"
                                                    class="absolute inset-0 w-full h-full object-cover object-[center_25%] scale-105">
                                            @else
                                                <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                                    <i data-lucide="user" class="w-16 h-16 text-primary-500"></i>
                                                </div>
                                            @endif
                                            @if ($item->graduation_year)
                                                <span class="absolute top-4 left-4 px-3 py-1 rounded-full
                                                            bg-white/95 backdrop-blur-sm text-xs font-bold text-surface-900 shadow-sm">
                                                    {{ $item->graduation_year }}
                                                </span>
                                            @endif
                                            @if ($item->department)
                                                <span class="absolute top-4 right-4 px-3 py-1 rounded-full
                                                            bg-primary-600 text-white text-xs font-semibold shadow-sm">
                                                    {{ $item->department }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="p-5 flex-1 flex flex-col">
                                            <h3 class="text-lg font-bold text-surface-900 leading-snug line-clamp-2">
                                                {{ $item->name }}
                                            </h3>
                                            @if ($item->achievement)
                                                <div class="mt-3 inline-flex items-center gap-1.5 self-start
                                                            px-2.5 py-1 rounded-full bg-accent-amber/15 text-accent-amber
                                                            text-xs font-semibold">
                                                    <i data-lucide="trophy" class="w-3 h-3"></i>
                                                    <span class="line-clamp-1">{{ $item->achievement }}</span>
                                                </div>
                                            @endif
                                            @if ($item->motto)
                                                <p class="mt-3 text-xs italic text-surface-500 line-clamp-2">
                                                    "{{ $item->motto }}"
                                                </p>
                                            @endif
                                            <div class="mt-auto pt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600">
                                                Lihat Profil
                                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination alumni-pagination !-bottom-2"></div>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('public.akademik.alumni.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary-600 hover:bg-primary-700
                                text-white text-sm font-semibold shadow-md hover:shadow-lg
                                transition-all duration-200 hover:-translate-y-0.5">
                        <span>Lihat Semua Alumni</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================ --}}
    {{-- BERITA --}}
    {{-- ============================================================ --}}
    @if ($news->isNotEmpty())
        @php
            $featuredNews = $news->first();
            $gridNews = $news->skip(1)->take(5);
        @endphp

        <section class="relative pt-16 pb-24 overflow-hidden">
            <div class="absolute top-12 right-16 w-5 h-5 rotate-45 bg-accent-amber/40 pointer-events-none hidden md:block"></div>
            <div class="absolute bottom-16 left-12 w-6 h-6 rotate-45 bg-primary-500/30 pointer-events-none hidden md:block"></div>

            <div class="relative max-w-6xl mx-auto px-4 z-10">
                <div class="flex flex-col md:flex-row items-center md:items-end justify-center md:justify-between gap-6 md:gap-4 mb-10 scroll-reveal text-center md:text-left">
                    <div class="w-full md:w-auto">
                        <span class="inline-block text-xs font-semibold text-primary-600 tracking-widest uppercase mb-2">
                            Kabar Terbaru
                        </span>
                        <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-surface-900 tracking-tight">
                            Informasi & Artikel
                        </h2>
                    </div>
                    <a href="{{ route('public.news.index') }}"
                        class="group inline-flex items-center gap-2 text-sm font-semibold text-primary-600
                            hover:text-primary-700 transition-colors">
                        <span>Semua Berita</span>
                        <i data-lucide="arrow-right"
                            class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>
                </div>

                <div class="hidden md:grid grid-cols-12 gap-5 md:gap-6">
                    @if ($featuredNews)
                        <a href="{{ route('public.news.show', $featuredNews->slug) }}"
                            class="group md:col-span-7 block">
                            <div class="relative overflow-hidden rounded-3xl aspect-[16/11] bg-surface-100">
                                @if ($featuredNews->image_url)
                                    <img src="{{ $featuredNews->image_url }}" alt="{{ $featuredNews->title }}"
                                        loading="lazy" width="800" height="550"
                                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                        <i data-lucide="newspaper" class="w-16 h-16 text-primary-400"></i>
                                    </div>
                                @endif
                                <div class="absolute top-5 left-5 px-3 py-1.5 rounded-full
                                        bg-accent-amber text-surface-900 text-[10px] font-bold uppercase tracking-wider shadow-lg">
                                    Berita Utama
                                </div>
                            </div>
                            <div class="mt-6">
                                <h3 class="text-2xl lg:text-3xl font-bold leading-tight text-surface-900
                                        group-hover:text-primary-700 transition-colors line-clamp-2">
                                    {{ $featuredNews->title }}
                                </h3>
                                <p class="mt-3 text-sm text-surface-500 line-clamp-2 leading-relaxed">
                                    {{ Str::limit(strip_tags($featuredNews->content), 180) }}
                                </p>
                                <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-primary-600">
                                    Baca Selengkapnya
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </div>
                            </div>
                        </a>
                    @endif

                    @if ($gridNews->isNotEmpty())
                        <div class="md:col-span-5 space-y-4">
                            @foreach ($gridNews as $item)
                                <a href="{{ route('public.news.show', $item->slug) }}"
                                    class="group block bg-white/80 backdrop-blur-sm rounded-2xl
                                        border border-surface-200/70 overflow-hidden
                                        transition-all duration-500 hover:-translate-y-1 hover:shadow-lg">
                                    <div class="flex gap-3 p-3">
                                        <div class="relative shrink-0 w-28 h-28 rounded-xl overflow-hidden bg-surface-100">
                                            @if ($item->image_url)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                                    loading="lazy" width="160" height="160"
                                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                                    <i data-lucide="newspaper" class="w-6 h-6 text-primary-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0 flex flex-col">
                                            <p class="text-[10px] text-surface-400 uppercase tracking-wider font-medium">
                                                {{ $item->published_at?->translatedFormat('d M Y') }}
                                            </p>
                                            <h3 class="mt-1.5 text-sm lg:text-base font-bold leading-snug text-surface-900
                                                    group-hover:text-primary-700 transition-colors line-clamp-2">
                                                {{ $item->title }}
                                            </h3>
                                            <div class="mt-auto pt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-primary-600">
                                                Baca
                                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="md:hidden">
                    <div class="swiper news-swiper !overflow-visible !pb-12">
                        <div class="swiper-wrapper">
                            @foreach ($news->take(6) as $item)
                                <div class="swiper-slide !h-auto">
                                    <a href="{{ route('public.news.show', $item->slug) }}"
                                        class="group block bg-white rounded-3xl overflow-hidden
                                        border border-surface-200 h-full flex flex-col">
                                        <div class="relative h-52 bg-surface-100 overflow-hidden">
                                            @if ($item->image_url)
                                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                                    loading="lazy" width="400" height="300"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                                    <i data-lucide="newspaper" class="w-16 h-16 text-primary-400"></i>
                                                </div>
                                            @endif
                                            <span class="absolute top-4 left-4 px-3 py-1 rounded-full
                                                    bg-white/95 backdrop-blur-sm text-xs font-semibold text-surface-900 shadow-sm">
                                                {{ $item->published_at?->translatedFormat('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="p-5 flex-1 flex flex-col">
                                            <h3 class="text-lg font-bold text-surface-900 leading-snug line-clamp-3">
                                                {{ $item->title }}
                                            </h3>
                                            <p class="mt-3 text-xs text-surface-500 line-clamp-3 leading-relaxed flex-1">
                                                {{ Str::limit(strip_tags($item->content), 120) }}
                                            </p>
                                            <div class="mt-auto pt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600">
                                                Baca Selengkapnya
                                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination news-pagination !-bottom-2"></div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============================================================ --}}
    {{-- FAQ --}}
    {{-- ============================================================ --}}
    <section class="relative py-16 md:py-24 overflow-hidden">

        <div class="absolute top-16 left-12 w-6 h-6 rotate-45 bg-accent-amber/40 pointer-events-none hidden md:block"></div>
        <div class="absolute bottom-24 right-16 w-8 h-8 rotate-45 border-2 border-primary-400/20 pointer-events-none hidden lg:block"></div>
        <div class="absolute top-1/2 -right-2 w-3 h-3 rotate-45 bg-primary-500/50 pointer-events-none hidden md:block"></div>

        <div class="absolute -top-20 left-1/3 w-[500px] h-[500px] rounded-full
                    bg-gradient-to-br from-primary-200/20 to-transparent blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl mx-auto px-4">

            <div class="text-center mb-12 scroll-reveal">
                <span class="inline-block text-xs font-semibold text-primary-600 tracking-widest uppercase mb-2">
                    Tanya Jawab
                </span>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-surface-900 tracking-tight">
                    Pertanyaan yang <span class="text-primary-600">Sering Ditanyakan</span>
                </h2>
                <p class="mt-3 text-sm text-surface-500 max-w-lg mx-auto">
                    Belum menemukan jawaban? Hubungi kami atau cek panduan SPMB.
                </p>
            </div>

            @php
                $faqs = [
                    [
                        'q' => 'Apakah potongan rambut laki-laki wajib botak?',
                        'a' => 'Tidak wajib botak. Potongan rambut siswa laki-laki di SMK Negeri 2 Karanganyar mengikuti aturan <strong>1 cm – 2 cm – 1 cm</strong>, yaitu: sisi samping kanan 1 cm, bagian atas 2 cm, dan sisi samping kiri 1 cm. Rambut harus rapi, tidak melewati kerah baju, tidak menutupi telinga dan alis, serta tidak diberi warna. Aturan ini bertujuan melatih kedisiplinan dan kerapian penampilan siswa.',
                    ],
                    [
                        'q' => 'Apakah kalau masuk SMK bisa lanjut kuliah?',
                        'a' => 'Sangat bisa. Lulusan SMK memiliki <strong>tiga pilihan utama</strong>: (1) <strong>Bekerja</strong> langsung di industri sesuai kompetensi keahlian, (2) <strong>Kuliah</strong> ke politeknik, universitas, atau institut — banyak alumni SMKN 2 Karanganyar yang diterima di PTN, PTS, hingga luar negeri, dan (3) <strong>Wirausaha</strong> dengan bekal keterampilan praktis.',
                    ],
                    [
                        'q' => 'Apa saja jurusan/kompetensi keahlian di SMKN 2 Karanganyar?',
                        'a' => 'SMK Negeri 2 Karanganyar memiliki beberapa kompetensi keahlian unggulan: <strong>Pengembangan Perangkat Lunak dan Gim (PPLG)</strong>, <strong>Teknik Otomotif</strong>, <strong>Teknik Mesin</strong>, dan <strong>Teknik Tekstil</strong>. Setiap jurusan dilengkapi bengkel/laboratorium standar industri dan kerja sama dengan perusahaan mitra untuk PKL.',
                    ],
                    [
                        'q' => 'Bagaimana cara mendaftar sebagai siswa baru (SPMB)?',
                        'a' => 'Pendaftaran dilakukan secara <strong>online</strong> melalui portal SPMB resmi Provinsi Jawa Tengah. Tahapannya: (1) Cek jadwal dan jalur pendaftaran, (2) Buat akun di portal SPMB, (3) Upload dokumen, (4) Pilih SMKN 2 Karanganyar, (5) Ikuti pengumuman dan daftar ulang. Info lengkap: <strong>Panduan SPMB</strong>.',
                    ],
                    [
                        'q' => 'Apakah ada program Praktik Kerja Lapangan (PKL)?',
                        'a' => 'Ada. PKL adalah bagian wajib kurikulum SMK yang biasanya dilaksanakan saat kelas XI selama 3–6 bulan. SMKN 2 Karanganyar bekerja sama dengan berbagai perusahaan mitra, mulai dari bengkel otomotif, pabrik tekstil, perusahaan IT, hingga startup digital.',
                    ],
                    [
                        'q' => 'Apa saja ekstrakurikuler yang tersedia?',
                        'a' => 'Beragam ekskul: <strong>Olahraga</strong> (futsal, basket, voli, badminton, panahan), <strong>Seni</strong> (paduan suara, tari, musik/band, teater), <strong>Akademik</strong> (KIR, olimpiade sains, robotik, coding club), <strong>Kepemimpinan</strong> (OSIS, Pramuka, PMR, Paskibra), dan <strong>Keagamaan</strong> (Rohis).',
                    ],
                ];
            @endphp

            <div class="space-y-3" id="faq-container">
                @foreach ($faqs as $i => $faq)
                    <div class="faq-item scroll-reveal bg-white rounded-2xl border border-surface-200/70
                            overflow-hidden transition-all duration-300
                            hover:border-primary-200 hover:shadow-md"
                        data-delay="{{ ($i % 3) * 75 }}">

                        <button type="button"
                            class="faq-trigger w-full flex items-center justify-between gap-4
                                   px-5 md:px-6 py-5 text-left
                                   hover:bg-primary-50/40 transition-colors duration-200"
                            aria-expanded="false">

                            <span class="flex items-start gap-3 flex-1 min-w-0">
                                <span class="shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700
                                         text-white text-xs font-bold flex items-center justify-center mt-0.5">
                                    {{ $i + 1 }}
                                </span>

                                <span class="text-sm md:text-base font-bold text-surface-900 leading-snug">
                                    {{ $faq['q'] }}
                                </span>
                            </span>

                            <span class="faq-icon shrink-0 w-8 h-8 rounded-full bg-surface-100
                                     flex items-center justify-center
                                     transition-all duration-300">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-surface-600"></i>
                            </span>
                        </button>

                        <div class="faq-panel overflow-hidden transition-all duration-300 ease-out"
                            style="max-height: 0; opacity: 0;">
                            <div class="px-5 md:px-6 pb-5 pt-1">
                                <div class="pl-10 pr-10 text-sm text-surface-600 leading-relaxed">
                                    {!! $faq['a'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10 text-center scroll-reveal">
                <p class="text-sm text-surface-500 mb-3">Masih ada pertanyaan lain?</p>
                <a href="{{ route('public.akademik.spmb.panduan') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-full
                      bg-gradient-to-br from-primary-600 to-primary-800
                      hover:shadow-xl hover:shadow-primary-500/30
                      text-white text-sm font-semibold transition-all duration-300 hover:-translate-y-0.5">
                    <i data-lucide="message-circle-question" class="w-4 h-4"></i>
                    Lihat Panduan SPMB
                </a>
            </div>
        </div>
    </section>

</main>

@endsection

@push('styles')
    @vite(['resources/css/home.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
@endpush