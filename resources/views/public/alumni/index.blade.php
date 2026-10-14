@extends('layouts.public')

@section('title', 'Alumni & Lulusan Terbaik — SMK Negeri 2 Karanganyar')
@section('description', 'Profil alumni terbaik SMK Negeri 2 Karanganyar — dari berbagai jurusan, dengan prestasi dan cerita menginspirasi.')

@section('content')

{{-- ============================================================ --}}
{{-- HERO --}}
{{-- ============================================================ --}}
<section class="relative overflow-hidden text-white">

    {{-- Background --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';"
             alt=""
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
    </div>

    {{-- Decorative blobs --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Content --}}
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">

        {{-- Breadcrumb --}}
        <nav class="mb-5 flex items-center justify-center gap-2 text-xs md:text-sm text-primary-100/90 animate-fade-in-up">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                <i data-lucide="home" class="w-3.5 h-3.5"></i>
                Beranda
            </a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
            <span class="text-white font-medium">Alumni</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-3 animate-fade-in-up stagger-1">
            Lulusan
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Terbaik
            </span>
        </h1>

        <p class="mx-auto max-w-xl text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-2">
            Profil alumni yang telah lulus dari SMK Negeri 2 Karanganyar — dari berbagai jurusan, dengan prestasi dan cerita yang menginspirasi.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ============================================================ --}}
{{-- LIST ALUMNI + SEARCH --}}
{{-- ============================================================ --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div id="page-content">

        {{-- Search Form --}}
        <form method="GET" action="{{ route('public.akademik.alumni.index') }}" class="mb-8">
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
                <div class="flex flex-col sm:flex-row gap-3">

                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <i data-lucide="search"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                        <input type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, jurusan, prestasi, atau motto..."
                            class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm text-surface-800
                                    outline-none transition
                                    focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                    </div>

                    {{-- Filter Tahun --}}
                    <select name="year"
                            class="min-w-[160px] rounded-xl border border-surface-200 bg-white px-4 py-3 text-sm text-surface-700
                                outline-none cursor-pointer
                                focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                        <option value="">Semua Tahun Lulus</option>
                        @foreach ($years as $y)
                            <option value="{{ $y }}" @selected((string) request('year') === (string) $y)>{{ $y }}</option>
                        @endforeach
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
                    @if (request()->hasAny(['search', 'year']))
                        <a href="{{ route('public.akademik.alumni.index') }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap
                                rounded-xl border border-surface-200 hover:border-red-300 hover:bg-red-50
                                px-4 py-3 text-sm font-medium text-surface-600 hover:text-red-600 transition">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                {{-- Active filter chip --}}
                @if (request('search') || request('year'))
                    <div class="mt-3 flex items-center gap-2 flex-wrap text-xs text-surface-500">
                        <span>Menampilkan hasil untuk:</span>
                        @if (request('search'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                                "{{ request('search') }}"
                            </span>
                        @endif
                        @if (request('year'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                                Tahun {{ request('year') }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </form>

        {{-- Header --}}
        <div class="mb-6 flex items-center gap-3">
            <h2 class="text-xl md:text-2xl font-bold text-surface-900">
                @if (request('search') || request('year'))
                    Hasil Pencarian
                @else
                    Semua Alumni
                @endif
            </h2>
            <span class="rounded-full bg-primary-50 px-3 py-1 text-sm font-semibold text-primary-700">
                {{ $alumni->total() }}
            </span>
        </div>

        {{-- Empty --}}
        @if ($alumni->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-surface-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-8 h-8 text-surface-400"></i>
                </div>
                @if (request('search') || request('year'))
                    <h3 class="text-base font-semibold text-surface-700 mb-1">Tidak ditemukan</h3>
                    <p class="text-sm text-surface-500 mb-4">
                        Tidak ada alumni yang cocok dengan filter Anda.
                    </p>
                    <a href="{{ route('public.akademik.alumni.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary-600 hover:bg-primary-700
                            text-white text-sm font-medium transition">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Tampilkan Semua Alumni
                    </a>
                @else
                    <p class="text-surface-500">Belum ada data alumni.</p>
                @endif
            </div>
        @else

            {{-- Grid Card Alumni --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($alumni as $i => $item)
                    <a href="{{ route('public.akademik.alumni.show', $item->slug) }}"
                    class="alumni-card group relative scroll-reveal bg-white rounded-3xl overflow-hidden
                            border border-surface-100 flex flex-col"
                    data-delay="{{ ($i % 3) * 100 }}">

                        {{-- Shadow lembut --}}
                        <div class="alumni-card__shadow" aria-hidden="true"></div>

                        {{-- Accent atas --}}
                        <div class="absolute top-0 left-0 right-0 h-20 bg-primary-600 opacity-[0.05] pointer-events-none"></div>

                        {{-- Foto --}}
                        <div class="relative pt-8 px-6 flex justify-center">
                            <div class="relative">
                                <div class="absolute -inset-1 rounded-2xl bg-primary-500 opacity-0
                                            group-hover:opacity-40 alumni-fade"></div>
                                <div class="alumni-photo relative w-[110px] h-[110px] rounded-2xl overflow-hidden
                                            bg-primary-50 ring-4 ring-white shadow-md">
                                    @if ($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                            <i data-lucide="user" class="w-12 h-12 text-primary-600"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="relative px-6 pt-5 pb-6 text-center flex-1 flex flex-col">
                            <h3 class="text-base font-bold text-surface-900 leading-snug line-clamp-2
                                    alumni-title">
                                {{ $item->name }}
                            </h3>

                            {{-- Info: tahun lulus + jurusan --}}
                            <div class="mt-2 flex items-center justify-center gap-2 flex-wrap text-xs">
                                @if ($item->graduation_year)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                bg-surface-100 text-surface-600 font-medium">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $item->graduation_year }}
                                    </span>
                                @endif
                                @if ($item->department)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                bg-primary-50 text-primary-700 font-medium">
                                        <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                        {{ $item->department }}
                                    </span>
                                @endif
                            </div>

                            {{-- Prestasi --}}
                            @if ($item->achievement)
                                <div class="mt-3 inline-flex items-center gap-1.5 self-center
                                            px-3 py-1 rounded-full bg-amber-50 text-amber-700
                                            text-xs font-semibold">
                                    <i data-lucide="trophy" class="w-3 h-3"></i>
                                    <span class="line-clamp-1">{{ $item->achievement }}</span>
                                </div>
                            @endif

                            {{-- Motto --}}
                            @if ($item->motto)
                                <p class="mt-3 text-xs italic text-surface-500 line-clamp-2 flex-1">
                                    "{{ $item->motto }}"
                                </p>
                            @endif

                            {{-- Divider --}}
                            <div class="alumni-divider mt-4 mx-auto w-12 h-[3px] rounded-full bg-primary-600"></div>

                            {{-- CTA --}}
                            <div class="mt-4 inline-flex items-center justify-center gap-1.5 text-xs font-semibold
                                        text-primary-600 group-hover:text-primary-700">
                                Lihat Profil
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5
                                    transition-transform duration-300 group-hover:translate-x-1"></i>
                            </div>
                        </div>

                        {{-- Accent bawah --}}
                        <div class="alumni-accent absolute bottom-0 left-0 right-0 h-1 bg-primary-600 origin-left"></div>
                    </a>
                @endforeach
            </div>

                    {{-- Pagination --}}
            @if ($alumni->hasPages())
                <div class="mt-10">
                    {{ $alumni->links() }}
                </div>
            @endif
        @endif

        </div> {{-- END: #page-content --}}

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- SKELETON — Grid card alumni (3 kolom)                       --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div id="page-skeleton" class="hidden">
            {{-- Filter skeleton --}}
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4 mb-8">
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1 h-12 animate-shimmer rounded-xl"></div>
                    <div class="w-40 h-12 animate-shimmer rounded-xl"></div>
                    <div class="w-24 h-12 animate-shimmer rounded-xl"></div>
                </div>
            </div>

            {{-- Header skeleton --}}
            <div class="mb-6 flex items-center gap-3">
                <div class="h-7 w-40 animate-shimmer rounded"></div>
                <div class="h-6 w-12 animate-shimmer rounded-full"></div>
            </div>

            {{-- Grid card alumni (6) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for ($i = 0; $i < 6; $i++)
                    <div class="bg-white rounded-3xl overflow-hidden border border-surface-100 flex flex-col">
                        {{-- Foto avatar --}}
                        <div class="pt-8 px-6 flex justify-center">
                            <div class="w-[110px] h-[110px] rounded-2xl animate-shimmer"></div>
                        </div>
                        {{-- Content --}}
                        <div class="px-6 pt-5 pb-6 text-center space-y-3">
                            <div class="h-5 w-3/4 mx-auto animate-shimmer rounded"></div>
                            <div class="flex justify-center gap-2">
                                <div class="h-5 w-16 animate-shimmer rounded-full"></div>
                                <div class="h-5 w-20 animate-shimmer rounded-full"></div>
                            </div>
                            <div class="h-5 w-24 mx-auto animate-shimmer rounded-full"></div>
                            <div class="h-4 w-full animate-shimmer rounded"></div>
                            <div class="h-4 w-2/3 mx-auto animate-shimmer rounded"></div>
                            <div class="h-[3px] w-12 mx-auto animate-shimmer rounded-full"></div>
                            <div class="h-3 w-20 mx-auto animate-shimmer rounded"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

</section>

@endsection

@push('styles')
<style>
    .scroll-reveal {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1),
                    transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .scroll-reveal.show {
        opacity: 1;
        transform: translateY(0);
    }
    .scroll-reveal[data-delay="100"] { transition-delay: 100ms; }
    .scroll-reveal[data-delay="200"] { transition-delay: 200ms; }

    /* ====== Alumni Card — smooth hover ====== */
    .alumni-card {
        transition:
            transform 0.6s cubic-bezier(0.16, 1, 0.3, 1),
            border-color 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
        transform: translateZ(0);
        backface-visibility: hidden;
    }
    .alumni-card:hover {
        transform: translate3d(0, -8px, 0);
        border-color: theme('colors.primary.200');
    }
    .alumni-card__shadow {
        position: absolute;
        inset: 0;
        border-radius: inherit;
        box-shadow:
            0 20px 40px -12px rgba(15, 118, 110, 0.18),
            0 8px 20px -8px rgba(15, 118, 110, 0.10);
        opacity: 0;
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
        z-index: 0;
    }
    .alumni-card:hover .alumni-card__shadow { opacity: 1; }

    .alumni-fade {
        transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: opacity;
    }

    .alumni-photo {
        transition: transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
        transform: translateZ(0);
    }
    .alumni-card:hover .alumni-photo {
        transform: translate3d(0, -4px, 0) scale(1.03);
    }

    .alumni-title { transition: color 0.4s ease-out; }
    .alumni-card:hover .alumni-title { color: theme('colors.primary.700'); }

    .alumni-divider {
        transform-origin: center;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
    }
    .alumni-card:hover .alumni-divider { transform: scaleX(1.7); }

    .alumni-accent {
        transform: scaleX(0);
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        will-change: transform;
    }
    .alumni-card:hover .alumni-accent { transform: scaleX(1); }

    @media (prefers-reduced-motion: reduce) {
        .scroll-reveal,
        .alumni-card,
        .alumni-card__shadow,
        .alumni-fade,
        .alumni-photo,
        .alumni-divider,
        .alumni-accent,
        .alumni-title {
            transition: none !important;
        }
        .alumni-card:hover { transform: none; }
    }
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

        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush