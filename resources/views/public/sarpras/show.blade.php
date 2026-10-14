@extends('layouts.public')

@section('title', 'Virtual Tour — ' . $room->name)

@section('content')
<section class="w-full bg-[#f8f7fc]">
    <div class="max-w-6xl mx-auto px-4 py-10 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-[#6845B9]/20 bg-[#6845B9]/5 px-3 py-1 text-[11px] font-semibold uppercase tracking-wider text-[#6845B9]">
                <i data-lucide="rotate-3d" class="w-3.5 h-3.5"></i>
                Virtual Tour
            </span>
            <h1 class="mt-4 text-2xl font-extrabold tracking-tight text-gray-900 sm:text-3xl">
                {{ $room->name }}
            </h1>
            <p class="mt-2 text-sm font-medium text-[#6845B9]">
                {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
                Jelajahi ruangan secara virtual dalam tampilan 360°
            </p>
        </div>

        {{-- VIRTUAL TOUR --}}
        <div class="mx-auto mt-8 max-w-4xl">
            <div class="rounded-2xl border border-gray-200 bg-white p-2 shadow-sm ring-1 ring-black/[0.02] sm:p-3">
                <div class="relative overflow-hidden rounded-xl bg-gray-50">
                    @php $panoramaSrc = $room->panorama; @endphp

                    @if ($panoramaSrc)
                        @php
                            $isEmbedUrl = str_contains($panoramaSrc, 'kuula.co')
                                    || str_contains($panoramaSrc, 'google.com/maps')
                                    || str_contains($panoramaSrc, 'youtube.com/embed')
                                    || str_contains($panoramaSrc, 'momento360.com');
                        @endphp

                        @if ($isEmbedUrl)
                            <iframe src="{{ $panoramaSrc }}"
                                    class="h-[420px] w-full sm:h-[500px]"
                                    style="border:0;"
                                    frameborder="0"
                                    allowfullscreen></iframe>
                        @else
                            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css">
                            <div id="panorama" style="width:100%;height:500px;min-height:300px;"></div>
                            <script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
                            <script>
                                window.addEventListener('load', function () {
                                    var el = document.getElementById('panorama');
                                    if (!el || typeof pannellum === 'undefined') return;
                                    pannellum.viewer('panorama', {
                                        type: 'equirectangular',
                                        panorama: "{!! $panoramaSrc !!}",
                                        autoLoad: true,
                                        showControls: true,
                                        showZoomCtrl: true,
                                        showFullscreenCtrl: true,
                                        mouseZoom: true,
                                        doubleClickZoom: true,
                                        draggable: true,
                                        minHfov: 30,
                                        maxHfov: 120,
                                        hfov: 100,
                                        compass: true
                                    });
                                });
                            </script>
                        @endif
                    @elseif ($room->photo)
                        <img src="{{ asset('storage/' . $room->photo) }}"
                             alt="{{ $room->name }}"
                             class="h-[420px] w-full object-cover sm:h-[500px]">
                    @else
                        <div class="flex h-[420px] flex-col items-center justify-center gap-3 bg-[#f3f0fa] sm:h-[500px]">
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-white shadow-sm">
                                <i data-lucide="image-off" class="w-6 h-6 text-[#6845B9]"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Virtual tour belum tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFORMASI RUANGAN --}}
        <section class="mt-10">
            <div class="rounded-2xl border border-gray-200 bg-white px-5 py-8 shadow-sm ring-1 ring-black/[0.02] sm:px-10 sm:py-10">

                <div class="text-center">
                    <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Informasi Ruangan</h2>
                    <div class="mx-auto mt-2 h-1 w-12 rounded-full bg-[#6845B9]"></div>
                </div>

                <div class="mt-10 grid items-start gap-8 lg:grid-cols-[1fr_1.1fr] lg:gap-10">

                    {{-- DESKRIPSI --}}
                    <div>
                        <h3 class="mb-5 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            DESKRIPSI
                        </h3>
                        <div class="rounded-xl border border-gray-200 bg-[#faf9fd] p-5 sm:p-6">
                            
                            <p class="text-sm leading-relaxed text-gray-700 sm:text-[15px]">
                                {{ $room->description ?: $room->name . ' merupakan salah satu ruangan di ' . (\App\Models\SchoolProfile::current()?->school_name ?? 'sekolah ini') . ' yang digunakan untuk kegiatan pembelajaran.' }}
                            </p>

                            <dl class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-200 pt-5 text-xs">
                                @if ($room->floor)
                                    <div>
                                        <dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Lantai</dt>
                                        <dd class="mt-0.5 text-sm font-semibold text-gray-800">{{ $room->floor }}</dd>
                                    </div>
                                @endif
                                @if ($room->capacity)
                                    <div>
                                        <dt class="text-[11px] font-medium uppercase tracking-wide text-gray-400">Kapasitas</dt>
                                        <dd class="mt-0.5 text-sm font-semibold text-gray-800">{{ $room->capacity }} orang</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                    

                    {{-- FASILITAS --}}
                    <div id="fasilitas">
                        {{-- HEADER + BADGE --}}
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                                Fasilitas
                            </h3>
                            @if ($items->total() > 0)
                                <span class="rounded-full bg-[#6845B9]/10 px-2.5 py-1 text-[10px] font-semibold text-[#6845B9]">
                                    {{ $items->total() }} Item
                                </span>
                            @endif
                        </div>

                        @if ($items->total() === 0 && $search === '')
                            {{-- Belum ada data sama sekali --}}
                            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
                                <p class="text-sm text-gray-400">Belum ada data fasilitas.</p>
                            </div>
                        @else
                            {{-- SEARCHBAR --}}
                            <form method="GET" action="{{ url()->current() }}#fasilitas" class="mb-4">
                                @foreach (request()->except(['fas_q', 'fas_page']) as $k => $v)
                                    @if (is_scalar($v))
                                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                                    @endif
                                @endforeach

                                <div class="rounded-lg border border-gray-200 bg-[#faf9fd] p-3">
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
                                            </svg>
                                        </span>
                                        <input type="text" name="fas_q" value="{{ $search }}"
                                            placeholder="Cari nama fasilitas..."
                                            class="w-full pl-9 pr-24 py-2 text-sm border border-gray-200 rounded-lg bg-white
                                                    focus:ring-2 focus:ring-[#6845B9]/30 focus:border-[#6845B9]">
                                        <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-1">
                                            @if ($search !== '')
                                                <a href="{{ request()->fullUrlWithQuery(['fas_q' => null, 'fas_page' => null]) }}#fasilitas"
                                                class="px-2 py-1 rounded-md text-[11px] font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                                                    Reset
                                                </a>
                                            @endif
                                            <button type="submit"
                                                    class="px-2.5 py-1 rounded-md bg-[#6845B9] text-white text-[11px] font-medium hover:bg-[#5a3aa0] transition">
                                                Cari
                                            </button>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-[11px] text-gray-400">
                                        Menampilkan
                                        <span class="font-semibold text-gray-600">{{ $items->firstItem() ?? 0 }}</span>–<span class="font-semibold text-gray-600">{{ $items->lastItem() ?? 0 }}</span>
                                        dari
                                        <span class="font-semibold text-gray-600">{{ $items->total() }}</span>
                                        item
                                        @if ($search !== '')
                                            <span>(hasil pencarian “{{ $search }}”)</span>
                                        @endif
                                    </p>
                                </div>
                            </form>

                            @if ($items->isEmpty())
                                {{-- Hasil search kosong --}}
                                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
                                    <p class="text-sm text-gray-400">Tidak ada fasilitas yang cocok dengan pencarian.</p>
                                </div>
                            @else
                                {{-- GRID --}}
                                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    @foreach ($items as $ir)
                                        <div class="group flex h-[115px] cursor-pointer flex-col items-center justify-center rounded-xl border border-gray-200 bg-white p-3 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-[#6845B9]/40 hover:shadow-md">
                                            <div class="text-[#6845B9] transition-transform duration-300 group-hover:scale-110">
                                                @if ($ir->item->photo)
                                                    <img src="{{ asset('storage/' . $ir->item->photo) }}"
                                                        alt="{{ $ir->item->name }}"
                                                        class="h-9 w-9 rounded object-cover">
                                                @else
                                                    <svg class="h-9 w-9" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="mt-2 line-clamp-2 text-center text-xs font-medium leading-4 text-gray-700">
                                                {{ $ir->item->name }}
                                            </span>
                                            <span class="mt-0.5 text-[10px] font-semibold text-[#6845B9]">
                                                {{ $ir->quantity }} Unit
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- PAGINATION --}}
                                @if ($items->hasPages())
                                    <nav aria-label="Pagination Fasilitas"
                                        class="mt-6 flex items-center justify-between gap-3 border-t border-gray-200 pt-4">

                                        <p class="text-[11px] text-gray-500">
                                            Halaman
                                            <span class="font-semibold text-gray-700">{{ $items->currentPage() }}</span>
                                            dari
                                            <span class="font-semibold text-gray-700">{{ $items->lastPage() }}</span>
                                        </p>

                                        <div class="flex items-center gap-1">
                                            {{-- Prev --}}
                                            @if ($items->onFirstPage())
                                                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-300">‹</span>
                                            @else
                                                <a href="{{ $items->previousPageUrl() }}#fasilitas"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-[#6845B9]/40 hover:text-[#6845B9]">‹</a>
                                            @endif

                                            {{-- Page numbers --}}
                                            @foreach ($items->getUrlRange(
                                                    max(1, $items->currentPage() - 2),
                                                    min($items->lastPage(), $items->currentPage() + 2)
                                                ) as $page => $url)
                                                @if ($page == $items->currentPage())
                                                    <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-[#6845B9] bg-[#6845B9] text-xs font-semibold text-white">
                                                        {{ $page }}
                                                    </span>
                                                @else
                                                    <a href="{{ $url }}#fasilitas"
                                                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-xs font-medium text-gray-600 transition hover:border-[#6845B9]/40 hover:text-[#6845B9]">
                                                        {{ $page }}
                                                    </a>
                                                @endif
                                            @endforeach

                                            {{-- Next --}}
                                            @if ($items->hasMorePages())
                                                <a href="{{ $items->nextPageUrl() }}#fasilitas"
                                                class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-600 transition hover:border-[#6845B9]/40 hover:text-[#6845B9]">›</a>
                                            @else
                                                <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 text-gray-300">›</span>
                                            @endif
                                        </div>
                                    </nav>
                                @endif
                            @endif
                        @endif
                    </div>

                </div>
            </div>
        </section>

    </div>
</section>
@endsection