@extends('layouts.public')

@section('title', $alumni->name . ' — Alumni SMK Negeri 2 Karanganyar')
@section('description', Str::limit(strip_tags($alumni->description ?? $alumni->motto ?? 'Profil alumni SMK Negeri 2 Karanganyar'), 150))

@section('content')

<div class="bg-surface-50">

    {{-- ============================================================ --}}
    {{-- HERO --}}
    {{-- ============================================================ --}}
    <section class="relative overflow-hidden text-white">

        {{-- Background Foto Sekolah --}}
        <div class="absolute inset-0">
            <img src="{{ asset('images/fotosekolah.webp') }}"
                onerror="this.style.display='none';"
                alt=""
                class="w-full h-full object-cover object-center">
            {{-- Overlay gradient halus --}}
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/90 via-primary-800/80 to-primary-900/90"></div>
            {{-- Vignette bawah untuk transisi ke konten --}}
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-surface-50 to-transparent"></div>
        </div>

        {{-- Dekorasi blob --}}
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -left-32 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Content --}}
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 md:pt-32 md:pb-28">

            {{-- Breadcrumb --}}
            <nav class="mt-4 md:mt-6 mb-8 flex items-center flex-wrap gap-2 text-xs md:text-sm text-primary-100/90">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    Beranda
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60 shrink-0"></i>
                <a href="{{ route('public.akademik.alumni.index') }}" class="hover:text-white transition-colors">
                    Alumni
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60 shrink-0"></i>
                <span class="text-white font-medium truncate max-w-[160px] sm:max-w-[240px]">{{ $alumni->name }}</span>
            </nav>

            {{-- Profile --}}
            <div class="flex flex-row items-center gap-4 md:gap-5">

                {{-- Foto Alumni --}}
                <div class="shrink-0 animate-fade-in-up">
                    <div
                        style="width: 110px; height: 110px;"
                        class="relative rounded-2xl overflow-hidden
                            ring-4 ring-white/25 shadow-xl shadow-primary-900/40
                            bg-primary-100 md:!w-[130px] md:!h-[130px]"
                    >
                        @if ($alumni->photo)
                            <img
                                src="{{ asset('storage/' . $alumni->photo) }}"
                                alt="{{ $alumni->name }}"
                                style="width: 100%; height: 100%; object-fit: cover;"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-primary-100">
                                <i data-lucide="user" class="w-12 h-12 text-primary-600"></i>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0 text-left animate-fade-in-up stagger-1">
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold leading-tight tracking-tight drop-shadow-lg">
                        {{ $alumni->name }}
                    </h1>

                    {{-- Meta chips --}}
                    @if ($alumni->graduation_year || $alumni->department || $alumni->achievement)
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            @if ($alumni->graduation_year)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                            bg-white/15 backdrop-blur-sm border border-white/25
                                            text-xs font-medium whitespace-nowrap">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    Lulus {{ $alumni->graduation_year }}
                                </span>
                            @endif

                            @if ($alumni->department)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                            bg-white/15 backdrop-blur-sm border border-white/25
                                            text-xs font-medium whitespace-nowrap">
                                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                                    {{ $alumni->department }}
                                </span>
                            @endif

                            @if ($alumni->achievement)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full
                                            bg-white/15 backdrop-blur-sm border border-white/25
                                            text-xs font-medium whitespace-nowrap">
                                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                                    {{ $alumni->achievement }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <x-public.wave />
    </section>

    {{-- ============================================================ --}}
    {{-- CONTENT --}}
    {{-- ============================================================ --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Motto — SOLID, tanpa gradient --}}
                @if ($alumni->motto)
                    <div class="relative bg-primary-700 rounded-2xl p-6 md:p-8 text-white shadow-lg overflow-hidden">


                        <div class="relative">
                            <div class="w-10 h-10 rounded-xl bg-white/15 ring-1 ring-white/20
                                        flex items-center justify-center mb-4">
                                <i data-lucide="quote" class="w-5 h-5"></i>
                            </div>
                            <p class="text-base md:text-lg italic leading-relaxed font-medium">
                                "{{ $alumni->motto }}"
                            </p>
                            <p class="mt-4 text-xs md:text-sm text-primary-100/80">— {{ $alumni->name }}</p>
                        </div>
                    </div>
                @endif

                {{-- Deskripsi --}}
                @if ($alumni->description)
                    <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-5">
                            <div>
                                <h2 class="text-lg md:text-xl font-bold text-surface-900">Tentang</h2>
                                <p class="text-xs text-surface-500 mt-0.5">Profil singkat alumni</p>
                            </div>
                        </div>

                        <div class="prose prose-sm md:prose-base max-w-none text-surface-700 leading-relaxed break-words overflow-wrap-anywhere">
                            {!! nl2br(e($alumni->description)) !!}
                        </div>
                    </div>
                @endif

            </div>

            {{-- Kolom Kanan --}}
            <div class="space-y-5">

                {{-- Info Card --}}
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                            <i data-lucide="info" class="w-5 h-5 text-primary-600"></i>
                        </div>
                        <h3 class="text-sm font-bold text-surface-900 uppercase tracking-wider">
                            Informasi
                        </h3>
                    </div>

                    <dl class="space-y-4 text-sm">
                        @if ($alumni->name)
                            <div>
                                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Nama Lengkap</dt>
                                <dd class="font-semibold text-surface-900">{{ $alumni->name }}</dd>
                            </div>
                        @endif

                        @if ($alumni->graduation_year)
                            <div>
                                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Tahun Lulus</dt>
                                <dd class="font-semibold text-surface-900">{{ $alumni->graduation_year }}</dd>
                            </div>
                        @endif

                        @if ($alumni->department)
                            <div>
                                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Jurusan</dt>
                                <dd class="font-semibold text-surface-900">{{ $alumni->department }}</dd>
                            </div>
                        @endif

                        @if ($alumni->achievement)
                            <div>
                                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Prestasi</dt>
                                <dd class="font-semibold text-primary-700 inline-flex items-center gap-1.5">
                                    <i data-lucide="trophy" class="w-3.5 h-3.5"></i>
                                    {{ $alumni->achievement }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- CTA Back --}}
                <a href="{{ route('public.akademik.alumni.index') }}"
                   class="flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl
                          bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold
                          shadow-md hover:shadow-lg transition-all active:scale-95">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Daftar Alumni
                </a>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- ALUMNI TERKAIT --}}
        {{-- ============================================================ --}}
        @if ($related->isNotEmpty())
            <div class="mt-16">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                    <h2 class="text-xl md:text-2xl font-bold text-surface-900">Alumni Lainnya</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ route('public.akademik.alumni.show', $item->slug) }}"
                           class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                                  shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

                            {{-- Foto --}}
                            <div class="relative aspect-[4/3] bg-surface-100 overflow-hidden">
                                @if ($item->photo)
                                    <img src="{{ asset('storage/' . $item->photo) }}"
                                         alt="{{ $item->name }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                        <i data-lucide="user" class="w-12 h-12 text-primary-400"></i>
                                    </div>
                                @endif

                                {{-- Badge tahun --}}
                                @if ($item->graduation_year)
                                    <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                 bg-white/95 backdrop-blur-sm text-primary-700 text-[10px] font-bold shadow-sm">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $item->graduation_year }}
                                    </span>
                                @endif
                            </div>

                            <div class="p-4">
                                <h3 class="line-clamp-1 font-bold text-surface-900 text-sm md:text-base
                                           group-hover:text-primary-600 transition-colors leading-snug">
                                    {{ $item->name }}
                                </h3>

                                @if ($item->department)
                                    <p class="mt-1 text-xs text-surface-500 line-clamp-1">
                                        {{ $item->department }}
                                    </p>
                                @endif

                                @if ($item->achievement)
                                    <p class="mt-2 inline-flex items-center gap-1 text-[11px] text-primary-700 font-semibold">
                                        <i data-lucide="trophy" class="w-3 h-3"></i>
                                        <span class="line-clamp-1">{{ $item->achievement }}</span>
                                    </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush