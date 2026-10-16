@extends('layouts.public')

@section('title', 'Berita Terbaru ')

@php $schoolLogo = \App\Models\SchoolProfile::current()?->logo; @endphp

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

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-3 animate-fade-in-up stagger-1">
            Berita
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Terkini
            </span>
        </h1>

        <p class="mx-auto max-w-xl text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-2">
            Update terbaru seputar kegiatan, prestasi, dan informasi penting sekolah.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ============================================================ --}}
{{-- LIST BERITA + SEARCH --}}
{{-- ============================================================ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- KONTEN ASLI (dibungkus untuk skeleton)                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="page-content">

    {{-- Search Form --}}
    <form method="GET" action="{{ route('public.news.index') }}" class="mb-8">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-3">

                {{-- Search Input --}}
                <div class="relative flex-1">
                    <i data-lucide="search"
                       class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari berdasarkan {{ $searchBy === 'penulis' ? 'penulis' : ($searchBy === 'konten' ? 'isi konten' : 'judul') }}..."
                           class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm text-surface-800
                                  outline-none transition
                                  focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                </div>

                {{-- Filter Dropdown --}}
                <select name="search_by"
                        class="min-w-[130px] rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm text-surface-700
                               outline-none cursor-pointer
                               focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                    <option value="judul"   @selected($searchBy === 'judul')>Judul</option>
                    <option value="penulis" @selected($searchBy === 'penulis')>Penulis</option>
                    <option value="konten"  @selected($searchBy === 'konten')>Isi Konten</option>
                </select>

                {{-- Submit --}}
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                               rounded-xl bg-primary-600 hover:bg-primary-700 px-5 py-3
                               text-sm font-medium text-white transition-all active:scale-95">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>

                {{-- Reset --}}
                @if (request()->hasAny(['search']))
                    <a href="{{ route('public.news.index') }}"
                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                              rounded-xl border border-surface-200 hover:border-red-300 hover:bg-red-50
                              px-4 py-3 text-sm font-medium text-surface-600 hover:text-red-600 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

            {{-- Active filter chip --}}
            @if (request('search'))
                <div class="mt-3 flex items-center gap-2 text-xs text-surface-500">
                    <span>Menampilkan hasil untuk:</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                        "{{ request('search') }}" ({{ $searchBy }})
                    </span>
                </div>
            @endif
        </div>
    </form>

    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <h2 class="text-xl md:text-2xl font-bold text-surface-900">
            @if (request('search'))
                Hasil Pencarian
            @else
                Semua Berita
            @endif
        </h2>
        <span class="rounded-full bg-primary-50 px-3 py-1 text-sm font-semibold text-primary-700">
            {{ $news->total() }}
        </span>
    </div>

    {{-- Empty --}}
    @if ($news->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70 shadow-sm">
            <div class="w-16 h-16 rounded-full bg-surface-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="search-x" class="w-8 h-8 text-surface-400"></i>
            </div>
            @if (request('search'))
                <h3 class="text-base font-semibold text-surface-700 mb-1">Tidak ditemukan</h3>
                <p class="text-sm text-surface-500 mb-4">
                    Tidak ada berita dengan kata kunci "{{ request('search') }}".
                </p>
                <a href="{{ route('public.news.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                          text-white text-sm font-medium transition">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Tampilkan Semua Berita
                </a>
            @else
                <p class="text-surface-500">Belum ada berita yang dipublikasikan.</p>
            @endif
        </div>
    @else

        {{-- List Berita --}}
        <div class="space-y-5">
            @foreach ($news as $i => $item)
                <article class="group flex flex-col overflow-hidden rounded-2xl border border-surface-200/70
                                bg-white shadow-sm hover:shadow-lg transition-all duration-300
                                hover:-translate-y-0.5 sm:flex-row
                                scroll-reveal" data-delay="{{ ($i % 3) * 100 }}">

                    {{-- Thumbnail --}}
                    <a href="{{ route('public.news.show', $item->slug) }}"
                    class="relative block h-48 w-full shrink-0 sm:h-auto sm:w-44 md:w-52 overflow-hidden bg-surface-100">
                        @if ($item->image)
                            <img src="{{ $item->image_url }}"
                                alt="{{ $item->title }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                loading="lazy">
                        @else
                            <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                <i data-lucide="newspaper" class="w-12 h-12 text-primary-400"></i>
                            </div>
                        @endif
                    </a>

                    {{-- Content --}}
                    <div class="flex flex-1 flex-col justify-between p-5">

                        <div>
                            {{-- Meta --}}
                            <div class="mb-2.5 flex items-center gap-3 flex-wrap">
                                @if ($item->published_at)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-surface-400">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ $item->published_at->translatedFormat('d M Y') }}
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 text-xs text-surface-400">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    {{ $item->author?->name ?? 'Admin' }}
                                </span>
                            </div>

                            {{-- Title --}}
                            <a href="{{ route('public.news.show', $item->slug) }}">
                                <h3 class="mb-2 line-clamp-2 text-base sm:text-lg font-bold text-surface-900
                                           group-hover:text-primary-600 transition-colors">
                                    {{ $item->title }}
                                </h3>
                            </a>

                            {{-- Excerpt --}}
                            <p class="line-clamp-2 text-sm text-surface-500 leading-relaxed">
                                {{ Str::limit(strip_tags($item->content), 140) }}
                            </p>
                        </div>

                        {{-- CTA --}}
                        <div class="mt-4">
                            <a href="{{ route('public.news.show', $item->slug) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-primary-600 hover:bg-primary-700
                                      px-4 py-2 text-xs sm:text-sm font-semibold text-white
                                      transition-all duration-200 active:scale-95">
                                Selengkapnya
                                <i data-lucide="arrow-right"
                                   class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5"></i>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

                {{-- Pagination --}}
        @if ($news->hasPages())
            <div class="mt-10">
                {{ $news->links() }}
            </div>
        @endif
    @endif

    </div> {{-- END: #page-content --}}

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SKELETON (muncul saat navigasi/filter)                     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div id="page-skeleton" class="hidden">
        {{-- Skeleton form filter --}}
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 h-12 animate-shimmer rounded-xl"></div>
                <div class="w-32 h-12 animate-shimmer rounded-xl"></div>
                <div class="w-24 h-12 animate-shimmer rounded-xl"></div>
            </div>
        </div>

        {{-- Skeleton header --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="h-7 w-48 animate-shimmer rounded"></div>
            <div class="h-6 w-12 animate-shimmer rounded-full"></div>
        </div>

        {{-- Skeleton list berita (5 item) --}}
        <div class="space-y-5">
            @for ($i = 0; $i < 5; $i++)
                <div class="flex flex-col sm:flex-row overflow-hidden rounded-2xl border border-surface-200/70 bg-white">
                    <div class="h-48 w-full sm:h-auto sm:w-44 md:w-52 shrink-0 animate-shimmer"></div>
                    <div class="flex-1 p-5 space-y-3">
                        <div class="flex gap-3">
                            <div class="h-3 w-24 animate-shimmer rounded"></div>
                            <div class="h-3 w-20 animate-shimmer rounded"></div>
                        </div>
                        <div class="h-5 w-3/4 animate-shimmer rounded"></div>
                        <div class="h-4 w-full animate-shimmer rounded"></div>
                        <div class="h-4 w-2/3 animate-shimmer rounded"></div>
                        <div class="h-8 w-28 animate-shimmer rounded-lg mt-3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

</section>

@endsection