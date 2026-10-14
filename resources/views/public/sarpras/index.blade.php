@extends('layouts.public')

@section('title', 'Sarana dan Prasarana ')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden text-white">

    {{-- Background Image + Blur --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';"
             alt=""
             width="1200" height="400" fetchpriority="high"
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
    </div>

    {{-- Hero Content --}}
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-16 text-center">

        {{-- Breadcrumb --}}
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5 mt-4 animate-fade-in-up">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Sarana Prasarana</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            Sarana
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Prasarana
            </span>
        </h1>

        {{-- Subtitle --}}
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Jelajahi berbagai sarana dan prasarana yang tersedia di
            {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
            melalui Virtual Tour.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- KONTEN ASLI (dibungkus #page-content)                       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="page-content">

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">

    {{-- Tombol Aksi --}}
    <div class="flex flex-wrap justify-end gap-2 mb-5">
        <a href="{{ route('public.rooms.map') }}"
           class="group inline-flex items-center gap-2 rounded-xl bg-white border-2 border-primary-500
                  px-4 py-2.5 text-sm font-semibold text-primary-600
                  transition-all duration-300 hover:-translate-y-0.5 hover:bg-primary-600 hover:text-white
                  hover:shadow-md active:scale-95">
            <i data-lucide="map" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110"></i>
            <span>Lihat Peta</span>
        </a>

        <a href="{{ route('public.sarpras.pengaduan') }}"
           class="group inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold
                  text-white shadow-sm transition-all duration-300 hover:-translate-y-0.5 hover:bg-primary-700
                  hover:shadow-md active:scale-95">
            <i data-lucide="file-pen-line" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110"></i>
            <span>Buat Laporan</span>
            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('public.rooms.index') }}">
        <div class="rounded-2xl border border-surface-200/70 bg-white p-3 shadow-sm">

            <div class="flex flex-col sm:flex-row gap-2">

                <div class="relative flex-1">
                    <i data-lucide="search"
                       class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Cari sarana dan prasarana..."
                           class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm text-surface-800
                                  outline-none transition
                                  focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                </div>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                               rounded-xl bg-primary-600 hover:bg-primary-700 px-5 py-3
                               text-sm font-medium text-white transition-all duration-200
                               active:scale-[0.98]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>

                @if (request()->hasAny(['search']))
                    <a href="{{ route('public.rooms.index') }}"
                       class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                              rounded-xl border border-surface-200 hover:border-red-300 hover:bg-red-50
                              px-4 py-3 text-sm font-medium text-surface-600 hover:text-red-600 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>

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
</section>

{{-- LIST SECTION --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">

    @if ($rooms->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <i data-lucide="building-2" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
            <p class="text-surface-500">Belum ada data ruangan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($rooms as $room)
                <div class="group overflow-hidden rounded-2xl border border-surface-200/70 bg-white shadow-sm
                            transition duration-300 hover:-translate-y-1 hover:shadow-xl
                            scroll-reveal">

                    <div class="relative h-52 overflow-hidden bg-surface-100">
                        @if ($room->photo)
                            <img src="{{ asset('storage/' . $room->photo) }}" alt="{{ $room->name }}"
                                 loading="lazy" width="400" height="208"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary-600 to-primary-800">
                                <i data-lucide="building-2" class="w-20 h-20 text-white/40"></i>
                            </div>
                        @endif

                        <div class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-surface-700 shadow-sm backdrop-blur">
                            {{ ucfirst(str_replace('_', ' ', $room->condition->value)) }}
                        </div>

                        @if ($room->panorama_url || $room->panorama_file)
                            <div class="absolute right-4 top-4 rounded-full bg-primary-600/90 px-2.5 py-1 text-[10px] font-semibold text-white shadow-sm backdrop-blur">
                                Virtual Tour
                            </div>
                        @endif
                    </div>

                    <div class="p-5">
                        <h2 class="text-lg font-bold text-surface-900">{{ $room->name }}</h2>

                        <div class="mt-3 flex items-center gap-2 text-sm text-surface-500">
                            <i data-lucide="list" class="w-4 h-4 text-primary-600"></i>
                            <span>{{ $room->item_rooms_count }} Jenis Barang</span>
                        </div>

                        <a href="{{ route('public.rooms.show', $room->code) }}"
                           class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl
                                  bg-primary-600 px-4 py-3 text-sm font-semibold text-white
                                  transition hover:bg-primary-700">
                            Kunjungi Virtual Tour
                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($rooms->hasPages())
        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-surface-100 pt-6 sm:flex-row">
            <nav>
                {{ $rooms->links() }}
            </nav>
        </div>
    @endif

</section>

</div> {{-- END: #page-content --}}

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SKELETON — Grid card sarpras (3 kolom)                      --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="page-skeleton" class="hidden">

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">

        {{-- Tombol aksi skeleton --}}
        <div class="flex flex-wrap justify-end gap-2 mb-5">
            <div class="w-28 h-10 animate-shimmer rounded-xl"></div>
            <div class="w-32 h-10 animate-shimmer rounded-xl"></div>
        </div>

        {{-- Search skeleton --}}
        <div class="rounded-2xl border border-surface-200/70 bg-white p-3">
            <div class="flex flex-col sm:flex-row gap-2">
                <div class="flex-1 h-12 animate-shimmer rounded-xl"></div>
                <div class="w-24 h-12 animate-shimmer rounded-xl"></div>
            </div>
        </div>
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @for ($i = 0; $i < 6; $i++)
                <div class="overflow-hidden rounded-2xl border border-surface-200/70 bg-white">
                    <div class="h-52 animate-shimmer"></div>
                    <div class="p-5 space-y-3">
                        <div class="h-5 w-3/4 animate-shimmer rounded"></div>
                        <div class="h-4 w-2/3 animate-shimmer rounded"></div>
                        <div class="h-3 w-1/2 animate-shimmer rounded"></div>
                        <div class="h-11 w-full animate-shimmer rounded-xl mt-3"></div>
                    </div>
                </div>
            @endfor
        </div>
    </section>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
    });
</script>
@endpush

@push('styles')
<style>
    .scroll-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }
    .scroll-reveal.show { opacity: 1; transform: translateY(0); }
</style>
@endpush