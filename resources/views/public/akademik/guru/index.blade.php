@extends('layouts.public')

@section('title', 'Guru & Tenaga Kependidikan ')

@section('content')

{{-- HERO --}}
<section class="relative text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';"
             alt=""
             class="w-full h-full object-cover blur-[2px] brightness-[0.55]">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Guru & Tenaga Kependidikan</span>
        </nav>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight drop-shadow-lg">
            Guru & 
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Tenaga Kependidikan
            </span>
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 drop-shadow">
            Tenaga pendidik profesional yang berdedikasi mendidik generasi unggul.
        </p>
    </div>

    <x-public.wave />
</section>

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Search & Filter --}}
    <form method="GET" class="mb-6">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama atau NIP..."
                           class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                </div>

                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700
                               px-5 py-3 text-sm font-medium text-white transition-all active:scale-95">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Cari
                </button>

                @if (request()->hasAny(['search', 'competency']))
                    <a href="{{ route('public.akademik.guru.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-surface-200 px-4 py-3 text-sm text-surface-600 hover:bg-surface-50">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>

    {{-- Accordion Guru --}}
    @if ($teachers->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <i data-lucide="users" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
            <p class="text-surface-500">Belum ada data guru.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach ($teachers as $i => $guru)
                <div x-data="{ open: false }"
                     class="bg-white rounded-xl border border-surface-200/70 shadow-sm overflow-hidden
                            scroll-reveal transition-all duration-300" data-delay="{{ ($i % 5) * 60 }}">

                    {{-- Trigger --}}
                    <button type="button"
                            x-on:click="open = !open"
                            class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left hover:bg-primary-50/30 transition">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600
                                        flex items-center justify-center text-white text-sm font-bold shrink-0">
                                {{ strtoupper(substr($guru->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-surface-800 truncate">{{ $guru->name }}</p>
                                @if ($guru->position)
                                    <p class="text-xs text-surface-500 truncate">{{ $guru->position }}</p>
                                @endif
                            </div>
                        </div>
                        <i data-lucide="chevron-down"
                           class="w-5 h-5 text-primary-600 shrink-0 transition-transform duration-300"
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    {{-- Panel --}}
                    <div x-show="open" x-collapse x-cloak>
                        <div class="bg-gradient-to-br from-primary-50/40 to-white p-5 md:p-6 border-t border-surface-100">
                            <div class="flex flex-col md:flex-row items-center md:items-start gap-5 md:gap-6">

                                {{-- Foto --}}
                                <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl bg-white shadow-md ring-1 ring-surface-200
                                            flex items-center justify-center overflow-hidden shrink-0">
                                    @if ($guru->photo)
                                        <img src="{{ asset('storage/' . $guru->photo) }}"
                                             alt="{{ $guru->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary-100">
                                            <i data-lucide="user" class="w-12 h-12 text-primary-500"></i>
                                        </div>
                                    @endif
                                </div>

                                {{-- Data --}}
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 w-full">
                                    <div>
                                        <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">Nama</p>
                                        <p class="text-sm text-surface-800 font-medium mt-0.5">{{ $guru->name }}</p>
                                    </div>

                                    @if ($guru->nip)
                                        <div>
                                            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">NIP</p>
                                            <p class="text-sm text-surface-800 font-medium mt-0.5">{{ $guru->nip }}</p>
                                        </div>
                                    @endif

                                    @if ($guru->gender)
                                        <div>
                                            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">Jenis Kelamin</p>
                                            <p class="text-sm text-surface-800 font-medium mt-0.5">
                                                {{ $guru->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </p>
                                        </div>
                                    @endif

                                    @if ($guru->competency)
                                        <div>
                                            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">Kompetensi</p>
                                            <p class="text-sm text-surface-800 font-medium mt-0.5">{{ $guru->competency }}</p>
                                        </div>
                                    @endif

                                    @if ($guru->subject)
                                        <div class="sm:col-span-2">
                                            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">Mata Pelajaran</p>
                                            <p class="text-sm text-surface-800 font-medium mt-0.5">{{ $guru->subject }}</p>
                                        </div>
                                    @endif

                                    @if ($guru->email)
                                        <div class="sm:col-span-2">
                                            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">Email</p>
                                            <p class="text-sm text-surface-800 font-medium mt-0.5">{{ $guru->email }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($teachers->hasPages())
                <div class="mt-10 flex justify-center">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    @endif

</section>

@endsection

@push('styles')
<style>
    .scroll-reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.5s cubic-bezier(0.16,1,0.3,1), transform 0.5s cubic-bezier(0.16,1,0.3,1); }
    .scroll-reveal.show { opacity: 1; transform: translateY(0); }
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
        }, { threshold: 0.1 });
        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
    });
</script>
@endpush