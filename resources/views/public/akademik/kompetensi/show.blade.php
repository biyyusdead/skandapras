@extends('layouts.public')

@section('title', $department->name )

@section('content')

<div class="bg-gradient-to-b from-primary-50/40 via-white to-surface-50 min-h-screen">

    {{-- HERO --}}
    <section class="relative text-white overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/fotosekolah.webp') }}"
                 onerror="this.style.display='none';" alt=""
                 class="w-full h-full object-cover blur-[2px] brightness-[0.55]">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
        </div>
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <nav class="flex items-center gap-2 text-xs md:text-sm text-primary-100/90 mb-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <a href="{{ route('public.akademik.kompetensi.index') }}" class="hover:text-white transition-colors">Kompetensi</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-white font-medium">{{ $department->code }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                @if ($department->image)
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20
                                p-4 shrink-0 flex items-center justify-center">
                        <img src="{{ asset('storage/' . $department->image) }}"
                             alt="{{ $department->name }}"
                             class="max-h-full max-w-full object-contain drop-shadow-lg">
                    </div>
                @endif

                <div class="flex-1 text-center sm:text-left">
                    <p class="text-xs font-semibold text-primary-200 uppercase tracking-widest mb-2">
                        {{ $department->code }}
                    </p>
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight drop-shadow-lg">
                        {{ $department->name }}
                    </h1>
                </div>
            </div>
        </div>

        <x-public.wave />
    </section>

    {{-- CONTENT --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-6">

        {{-- Deskripsi --}}
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
            <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                Deskripsi
            </h2>
            <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                {{ $department->description }}
            </div>
        </div>

        {{-- Visi & Misi --}}
        @if ($department->vision || $department->mission)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @if ($department->vision)
                    <div class="bg-gradient-to-br from-primary-600 to-primary-800 text-white rounded-2xl p-6 shadow-lg">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20 flex items-center justify-center mb-4">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3">Visi</h3>
                        <p class="text-sm text-white/95 leading-relaxed">{{ $department->vision }}</p>
                    </div>
                @endif

                @if ($department->mission)
                    <div class="bg-gradient-to-br from-primary-700 to-primary-900 text-white rounded-2xl p-6 shadow-lg">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20 flex items-center justify-center mb-4">
                            <i data-lucide="target" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3">Misi</h3>
                        <ol class="space-y-2">
                            @foreach (array_filter(array_map('trim', explode("\n", $department->mission))) as $i => $m)
                                <li class="flex items-start gap-2 text-sm text-white/95 leading-relaxed">
                                    <span class="w-5 h-5 rounded-full bg-white/15 ring-1 ring-white/20 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                        {{ $i + 1 }}
                                    </span>
                                    <span>{{ $m }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            </div>
        @endif

        {{-- Galeri --}}
        @if ($department->gallery && count($department->gallery))
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
                <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                    <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                    Galeri Kegiatan
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($department->gallery as $img)
                        <img src="{{ asset('storage/' . $img) }}"
                             class="aspect-video w-full object-cover rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Jurusan Lain --}}
        @if ($others->isNotEmpty())
            <div>
                <h2 class="text-xl font-bold text-surface-900 mb-5 flex items-center gap-2">
                    <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                    Kompetensi Lainnya
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($others as $o)
                        <a href="{{ route('public.akademik.kompetensi.show', $o->slug) }}"
                           class="group bg-white rounded-xl border border-surface-200/70 overflow-hidden shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
                            <div class="aspect-square bg-surface-100 overflow-hidden flex items-center justify-center">
                                @if ($o->image)
                                    <img src="{{ asset('storage/' . $o->image) }}" alt="{{ $o->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <i data-lucide="layers" class="w-10 h-10 text-primary-400"></i>
                                @endif
                            </div>
                            <div class="p-3 text-center">
                                <p class="text-xs font-bold text-primary-600 uppercase tracking-wider">{{ $o->code }}</p>
                                <p class="text-xs text-surface-800 font-semibold truncate mt-0.5">{{ $o->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Back --}}
        <div class="text-center pt-4">
            <a href="{{ route('public.akademik.kompetensi.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 border-primary-500
                      text-primary-600 hover:bg-primary-600 hover:text-white text-sm font-semibold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Kompetensi Keahlian
            </a>
        </div>
    </section>
</div>

@endsection