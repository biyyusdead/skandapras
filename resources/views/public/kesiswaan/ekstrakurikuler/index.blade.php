@extends('layouts.public')

@section('title', 'Ekstrakurikuler ')

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
            <span class="text-white font-medium">Ekstrakurikuler</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            Ekstrakurikuler
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Berprestasi
            </span>
        </h1>

        {{-- Subtitle --}}
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Wadah pengembangan bakat, minat, dan karakter siswa di luar jam pelajaran.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ============================================================ --}}
{{-- CONTENT + SEARCH --}}
{{-- ============================================================ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Search --}}
    <form method="GET" class="mb-8">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau deskripsi ekstrakurikuler..."
                           class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm outline-none
                                  focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                </div>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700
                               px-5 py-3 text-sm font-medium text-white transition-all active:scale-95">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>
                @if (request('search'))
                    <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-4 py-3 text-sm text-surface-600 hover:bg-surface-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            {{-- Active filter chip --}}
            @if (request('search'))
                <div class="mt-3 flex items-center gap-2 text-xs text-surface-500">
                    <span>Menampilkan hasil untuk:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                        "{{ request('search') }}"
                    </span>
                </div>
            @endif
        </div>
    </form>

    {{-- List --}}
    @if ($extracurriculars->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <div class="w-16 h-16 rounded-full bg-surface-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8 text-surface-400"></i>
            </div>
            @if (request('search'))
                <h3 class="text-base font-semibold text-surface-700 mb-1">Tidak ditemukan</h3>
                <p class="text-sm text-surface-500 mb-4">
                    Tidak ada ekstrakurikuler dengan kata kunci "{{ request('search') }}".
                </p>
                <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                          text-white text-sm font-medium transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Tampilkan Semua
                </a>
            @else
                <p class="text-surface-500">Belum ada data ekstrakurikuler.</p>
            @endif
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($extracurriculars as $i => $item)
                <a href="{{ route('public.kesiswaan.ekstrakurikuler.show', $item) }}"
                   class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                          shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                          scroll-reveal" data-delay="{{ ($i % 3) * 100 }}">

                    <div class="relative aspect-[4/3] bg-surface-100 overflow-hidden">
                        @if ($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                <i data-lucide="activity" class="w-16 h-16 text-primary-500"></i>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>

                        <h3 class="absolute bottom-4 left-4 right-4 text-white font-bold text-lg leading-tight drop-shadow">
                            {{ $item->name }}
                        </h3>
                    </div>

                    <div class="p-5">
                        <p class="text-sm text-surface-500 line-clamp-3">
                            {{ $item->description }}
                        </p>

                        <div class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-primary-600
                                    group-hover:gap-2.5 transition-all">
                            Lihat Detail
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($extracurriculars->hasPages())
            <div class="mt-10">{{ $extracurriculars->links() }}</div>
        @endif
    @endif

</section>

@endsection
