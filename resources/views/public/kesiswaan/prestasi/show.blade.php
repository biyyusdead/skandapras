@extends('layouts.public')

@section('title', $achievement->title)
@section('description', Str::limit(strip_tags($achievement->description ?? ''), 150))

@section('content')

<div class="bg-gradient-to-b from-primary-50/40 via-white to-surface-50 min-h-screen">

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden text-white">

        {{-- Background Image + Blur --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/fotosekolah.webp') }}"
                 onerror="this.style.display='none';"
                 alt=""
                 class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-10 md:pt-20 md:pb-12">

            {{-- Breadcrumb --}}
            <nav class="flex flex-wrap items-center gap-2 text-xs md:text-sm text-primary-100/90 mb-6">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <a href="{{ route('public.kesiswaan.prestasi.index') }}" class="hover:text-white transition-colors">Prestasi</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-white font-medium">Detail</span>
            </nav>

            {{-- Meta badges --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 animate-fade-in-up">
                @if ($achievement->category)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-white/15 backdrop-blur-sm border border-white/25
                                 text-xs font-bold uppercase tracking-wider">
                        <i data-lucide="award" class="w-3.5 h-3.5"></i>
                        {{ $achievement->category }}
                    </span>
                @endif

                @if ($achievement->achievement_date)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-white/15 backdrop-blur-sm border border-white/25 text-xs font-medium">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        {{ $achievement->achievement_date->translatedFormat('d F Y') }}
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight
                       animate-fade-in-up stagger-1 drop-shadow-lg">
                {{ $achievement->title }}
            </h1>
        </div>

        <x-public.wave />
    </section>

    {{-- ============================================================ --}}
    {{-- CONTENT --}}
    {{-- ============================================================ --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-6">

        {{-- Foto Utama --}}
        @if ($achievement->image_url)
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-xl shadow-primary-900/5 overflow-hidden animate-fade-in-up">
                <div class="relative aspect-video bg-surface-100">
                    <img src="{{ $achievement->image_url }}"
                         alt="{{ $achievement->title }}"
                         class="w-full h-full object-cover">
                </div>
            </div>
        @endif

        {{-- Deskripsi --}}
        @if ($achievement->description)
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg shadow-primary-900/5 p-6 md:p-8 animate-fade-in-up stagger-1">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-surface-900">Deskripsi Prestasi</h2>
                        <p class="text-xs text-surface-500 mt-0.5">Detail informasi prestasi</p>
                    </div>
                </div>

                <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                    {{ $achievement->description }}
                </div>
            </div>
        @endif

        {{-- Info Grid — SEMUA ICON PRIMARY --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fade-in-up stagger-2">
            @if ($achievement->category)
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-md p-5 flex items-center gap-4
                            hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                        <i data-lucide="award" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-surface-400">Kategori</p>
                        <p class="text-sm font-bold text-surface-900 truncate">{{ $achievement->category }}</p>
                    </div>
                </div>
            @endif

            @if ($achievement->achievement_date)
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-md p-5 flex items-center gap-4
                            hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                        <i data-lucide="calendar" class="w-5 h-5 text-primary-600"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-surface-400">Tanggal</p>
                        <p class="text-sm font-bold text-surface-900">{{ $achievement->achievement_date->translatedFormat('d M Y') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-md p-5 flex items-center gap-4
                        hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                    <i data-lucide="building" class="w-5 h-5 text-primary-600"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-wider text-surface-400">Institusi</p>
                    <p class="text-sm font-bold text-surface-900 truncate">
                        {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- CTA — SOLID, TANPA GRADIENT --}}
        <div class="bg-primary-700 text-white rounded-2xl p-6 md:p-8
                    shadow-xl shadow-primary-500/20 animate-fade-in-up stagger-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1">
                    <h3 class="font-bold text-lg">Ingin meraih prestasi serupa?</h3>
                    <p class="text-sm text-primary-100/90 mt-1">
                        Bergabunglah dengan kami dan kembangkan potensi terbaik Anda.
                    </p>
                </div>
                <a href="{{ route('public.akademik.spmb.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-primary-700
                          hover:bg-primary-50 text-sm font-semibold transition-all shadow-md hover:shadow-lg
                          active:scale-95 shrink-0">
                    Info SPMB
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        {{-- Prestasi Terkait --}}
        @if ($related->isNotEmpty())
            <div class="pt-4 animate-fade-in-up stagger-4">
                <div class="flex items-center gap-3 mb-5">
                    <h2 class="text-xl font-bold text-surface-900">Prestasi Lainnya</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ route('public.kesiswaan.prestasi.show', $item) }}"
                           class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                                  shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

                            <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                        <i data-lucide="trophy" class="w-12 h-12 text-primary-500"></i>
                                    </div>
                                @endif

                                @if ($item->category)
                                    <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full
                                                bg-white/95 backdrop-blur-sm text-primary-700
                                                text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                        {{ $item->category }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                @if ($item->achievement_date)
                                    <p class="text-[10px] text-surface-400 mb-1">
                                        {{ $item->achievement_date->translatedFormat('d M Y') }}
                                    </p>
                                @endif
                                <h3 class="font-bold text-surface-900 text-sm line-clamp-2 leading-snug
                                           group-hover:text-primary-600 transition-colors">
                                    {{ $item->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back --}}
        <div class="text-center pt-6">
            <a href="{{ route('public.kesiswaan.prestasi.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 border-primary-500
                      text-primary-600 hover:bg-primary-600 hover:text-white text-sm font-semibold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar Prestasi
            </a>
        </div>

    </section>
</div>

@endsection