@extends('layouts.public')

@section('title', 'Prestasi ')

@section('content')

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
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">

        {{-- Breadcrumb --}}
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5 animate-fade-in-up">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Prestasi</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            Prestasi
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Gemilang
            </span>
        </h1>

        {{-- Subtitle --}}
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Berbagai prestasi yang telah diraih siswa-siswi SMK Negeri 2 Karanganyar di tingkat kabupaten, provinsi, dan nasional.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ============================================================ --}}
{{-- FILTER + LIST --}}
{{-- ============================================================ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div id="page-content">

        {{-- Filter --}}
        <form method="GET" class="mb-8">
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Cari prestasi..."
                            class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm outline-none
                                    focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                    </div>

                    <select name="category" class="rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm cursor-pointer
                                                focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" @selected(request('category') === $cat)>{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select name="year" class="rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm cursor-pointer
                                            focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua Tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>
                        @endforeach
                    </select>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700
                                px-5 py-3 text-sm font-medium text-white transition-all active:scale-95">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Filter
                    </button>

                    @if (request()->hasAny(['search', 'category', 'year']))
                        <a href="{{ route('public.kesiswaan.prestasi.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-4 py-3
                                text-sm text-surface-600 hover:bg-surface-50 transition">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- Grid Prestasi --}}
        @if ($achievements->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
                <div class="w-16 h-16 rounded-full bg-surface-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trophy" class="w-8 h-8 text-surface-400"></i>
                </div>
                <p class="text-surface-500">Belum ada data prestasi.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($achievements as $i => $item)
                    <a href="{{ route('public.kesiswaan.prestasi.show', $item) }}"
                        class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden shadow-sm hover:shadow-xl
                                hover:-translate-y-1 transition-all duration-300 block
                                scroll-reveal" data-delay="{{ ($i % 3) * 100 }}">
                            
                            {{-- Image --}}
                        <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                    <i data-lucide="trophy" class="w-16 h-16 text-primary-500"></i>
                                </div>
                            @endif

                            {{-- Category badge --}}
                            @if ($item->category)
                                <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full
                                            bg-white/95 backdrop-blur-sm text-primary-700
                                            text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                    {{ $item->category }}
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            @if ($item->achievement_date)
                                <p class="text-xs text-surface-400 mb-1.5 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    {{ $item->achievement_date->translatedFormat('d F Y') }}
                                </p>
                            @endif

                            <h3 class="font-bold text-surface-900 line-clamp-2 leading-snug">
                                {{ $item->title }}
                            </h3>

                            @if ($item->description)
                                <p class="mt-2 text-sm text-surface-500 line-clamp-3">
                                    {{ $item->description }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($achievements->hasPages())
                <div class="mt-10">{{ $achievements->links() }}</div>
            @endif
        @endif

        </div> {{-- END: #page-content --}}

        {{-- Skeleton --}}
        <div id="page-skeleton" class="hidden">
            {{-- Filter skeleton --}}
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4 mb-8">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 h-12 animate-shimmer rounded-xl"></div>
                    <div class="w-40 h-12 animate-shimmer rounded-xl"></div>
                    <div class="w-32 h-12 animate-shimmer rounded-xl"></div>
                    <div class="w-24 h-12 animate-shimmer rounded-xl"></div>
                </div>
            </div>

            {{-- Grid card skeleton (6 card) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for ($i = 0; $i < 6; $i++)
                    <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden">
                        <div class="aspect-[16/10] animate-shimmer"></div>
                        <div class="p-5 space-y-3">
                            <div class="h-3 w-24 animate-shimmer rounded"></div>
                            <div class="h-5 w-3/4 animate-shimmer rounded"></div>
                            <div class="h-4 w-full animate-shimmer rounded"></div>
                            <div class="h-4 w-2/3 animate-shimmer rounded"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

</section>

@endsection