@extends('layouts.public')

@section('title', 'PKL & BKK ')

@section('content')

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
            <span class="text-white font-medium">PKL & BKK</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            PKL & <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">BKK</span>
        </h1>

        {{-- Subtitle --}}
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Informasi Praktik Kerja Lapangan (PKL) dan Bursa Kerja Khusus (BKK) untuk siswa dan alumni.
        </p>
    </div>

    <x-public.wave />
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Search --}}
    <form method="GET" class="mb-8">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search" name="search" value="{{ request('search') }}"
                           placeholder="Cari perusahaan atau judul..."
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
                    <a href="{{ route('public.kesiswaan.pkl-bkk.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-4 py-3 text-sm text-surface-600 hover:bg-surface-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- List --}}
    @if ($internships->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <i data-lucide="briefcase" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
            <p class="text-surface-500">Belum ada data PKL & BKK.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($internships as $i => $item)
                <div class="group bg-white rounded-2xl border border-surface-200/70 p-5 shadow-sm
                            hover:shadow-lg transition-all duration-300
                            flex flex-col sm:flex-row gap-5
                            scroll-reveal" data-delay="{{ ($i % 3) * 100 }}">

                    {{-- Info --}}
                    <div class="flex-1 min-w-0 text-center sm:text-left">
                        <h3 class="font-bold text-surface-900 text-lg leading-tight">
                            {{ $item->title }}
                        </h3>

                        @if ($item->company)
                            <p class="text-primary-600 font-medium text-sm mt-1">
                                {{ $item->company }}
                            </p>
                        @endif

                        @if ($item->description)
                            <p class="text-sm text-surface-500 mt-3 line-clamp-2">
                                {{ $item->description }}
                            </p>
                        @endif

                        {{-- Info row --}}
                        <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-2 text-xs text-surface-500">
                            @if ($item->start_date)
                                <span class="inline-flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-primary-500"></i>
                                    {{ $item->start_date->translatedFormat('d M Y') }}
                                    @if ($item->end_date)
                                        — {{ $item->end_date->translatedFormat('d M Y') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="flex items-center justify-center sm:justify-end shrink-0">
                        <a href="{{ route('public.kesiswaan.pkl-bkk.show', $item) }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl
                                  bg-primary-600 hover:bg-primary-700 text-white
                                  text-sm font-medium transition-all active:scale-95">
                            Lihat Detail
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($internships->hasPages())
            <div class="mt-10">{{ $internships->links() }}</div>
        @endif
    @endif

</section>

@endsection

@push('styles')
<style>
    .scroll-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }
    .scroll-reveal.show { opacity: 1; transform: translateY(0); }
    .scroll-reveal[data-delay="100"] { transition-delay: 100ms; }
    .scroll-reveal[data-delay="200"] { transition-delay: 200ms; }
</style>
@endpush

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