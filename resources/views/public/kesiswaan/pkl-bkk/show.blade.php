@extends('layouts.public')

@section('title', $internship->title)
@section('description', Str::limit(strip_tags($internship->description ?? ''), 150))

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
                <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}" class="hover:text-white transition-colors">PKL & BKK</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-white font-medium">Detail</span>
            </nav>

            {{-- Meta badges --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 animate-fade-in-up">
                @if ($internship->company)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-white/15 backdrop-blur-sm border border-white/25
                                 text-xs font-bold uppercase tracking-wider">
                        <i data-lucide="building-2" class="w-3.5 h-3.5"></i>
                        {{ $internship->company }}
                    </span>
                @endif

                @if ($internship->start_date)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-white/15 backdrop-blur-sm border border-white/25 text-xs font-medium">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        {{ $internship->start_date->translatedFormat('d F Y') }}
                        @if ($internship->end_date)
                            — {{ $internship->end_date->translatedFormat('d F Y') }}
                        @endif
                    </span>
                @endif
            </div>

            {{-- Title --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight
                       animate-fade-in-up stagger-1 drop-shadow-lg">
                {{ $internship->title }}
            </h1>
        </div>

        <x-public.wave />
    </section>

    {{-- ============================================================ --}}
    {{-- CONTENT --}}
    {{-- ============================================================ --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-6">

        {{-- Info Card --}}
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg shadow-primary-900/5 p-6 md:p-8 animate-fade-in-up">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-11 h-11 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-primary-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-surface-900">Informasi</h2>
                    <p class="text-xs text-surface-500 mt-0.5">Detail PKL & BKK</p>
                </div>
            </div>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                @if ($internship->company)
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Perusahaan</dt>
                        <dd class="text-sm text-surface-800 font-medium">{{ $internship->company }}</dd>
                    </div>
                @endif

                @if ($internship->start_date)
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Tanggal Mulai</dt>
                        <dd class="text-sm text-surface-800 font-medium">
                            {{ $internship->start_date->translatedFormat('d F Y') }}
                        </dd>
                    </div>
                @endif

                @if ($internship->end_date)
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Tanggal Selesai</dt>
                        <dd class="text-sm text-surface-800 font-medium">
                            {{ $internship->end_date->translatedFormat('d F Y') }}
                        </dd>
                    </div>
                @endif

                @if ($internship->start_date && $internship->end_date)
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Durasi</dt>
                        <dd class="text-sm text-surface-800 font-medium">
                            {{ $internship->start_date->diffInDays($internship->end_date) }} hari
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Deskripsi --}}
        @if ($internship->description)
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg shadow-primary-900/5 p-6 md:p-8 animate-fade-in-up stagger-1">
                <div class="flex items-center gap-3 mb-5">
                    <div>
                        <h2 class="text-xl font-bold text-surface-900">Deskripsi</h2>
                        <p class="text-xs text-surface-500 mt-0.5">Detail informasi PKL & BKK</p>
                    </div>
                </div>

                <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                    {{ $internship->description }}
                </div>
            </div>
        @endif

        {{-- CTA --}}
        <div class="bg-primary-700 text-white rounded-2xl p-6 md:p-8
                    shadow-xl shadow-primary-500/20 animate-fade-in-up stagger-2">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1">
                    <h3 class="font-bold text-lg">Tertarik mendaftar?</h3>
                    <p class="text-sm text-primary-100/90 mt-1">
                        Hubungi BKK sekolah untuk informasi lebih lanjut.
                    </p>
                </div>
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
                   aria-label="Hubungi BKK via WhatsApp"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white text-primary-700
                          hover:bg-primary-50 text-sm font-semibold transition-all shadow-md hover:shadow-lg
                          active:scale-95 shrink-0">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    Hubungi BKK
                </a>
            </div>
        </div>

        {{-- Info Lainnya --}}
        @if ($others->isNotEmpty())
            <div class="pt-4 animate-fade-in-up stagger-3">
                <div class="flex items-center gap-3 mb-5">
                    <h2 class="text-xl font-bold text-surface-900">Info Lainnya</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($others as $o)
                        <a href="{{ route('public.kesiswaan.pkl-bkk.show', $o) }}"
                           class="group bg-white rounded-2xl border border-surface-200/70 p-5 shadow-sm
                                  hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-4">

                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-surface-800 text-sm line-clamp-2 group-hover:text-primary-600 transition-colors">
                                    {{ $o->title }}
                                </p>
                                @if ($o->company)
                                    <p class="text-xs text-surface-500 mt-0.5 truncate">{{ $o->company }}</p>
                                @endif
                            </div>

                            <i data-lucide="arrow-right" class="w-4 h-4 text-surface-400 group-hover:text-primary-600
                                group-hover:translate-x-0.5 transition-all shrink-0"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back --}}
        <div class="text-center pt-6">
            <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 border-primary-500
                      text-primary-600 hover:bg-primary-600 hover:text-white text-sm font-semibold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Daftar PKL & BKK
            </a>
        </div>

    </section>
</div>

@endsection