@extends('layouts.public')

@section('title', $extracurricular->name)

@section('content')

<div class="bg-gradient-to-b from-primary-50/40 via-white to-surface-50 min-h-screen">

    {{-- HERO --}}
    <section class="relative text-white overflow-hidden bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900">
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
            <nav class="flex items-center gap-2 text-xs md:text-sm text-primary-100/90 mb-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}" class="hover:text-white transition-colors">Ekstrakurikuler</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-white font-medium">{{ $extracurricular->name }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                @if ($extracurricular->image)
                    <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-2xl bg-white/10 backdrop-blur-md ring-1 ring-white/20
                                p-4 shrink-0">
                        <img src="{{ asset('storage/' . $extracurricular->image) }}"
                             alt="{{ $extracurricular->name }}"
                             class="w-full h-full object-contain drop-shadow-lg">
                    </div>
                @endif

                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight">
                        {{ $extracurricular->name }}
                    </h1>

                    @if ($extracurricular->coach)
                        <p class="mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full
                                  bg-white/10 backdrop-blur-sm border border-white/20 text-sm">
                            <i data-lucide="user" class="w-3.5 h-3.5"></i>
                            Pembina: {{ $extracurricular->coach }}
                        </p>
                    @endif

                    @if ($extracurricular->schedule)
                        <p class="mt-2 inline-flex items-center gap-2 px-3 py-1 rounded-full
                                  bg-white/10 backdrop-blur-sm border border-white/20 text-sm ml-0 sm:ml-2">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            {{ $extracurricular->schedule }}
                        </p>
                    @endif
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
                {{ $extracurricular->description }}
            </div>
        </div>

        {{-- Visi & Misi --}}
        @if ($extracurricular->vision || $extracurricular->mission)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @if ($extracurricular->vision)
                    <div class="bg-gradient-to-br from-primary-600 to-primary-800 text-white rounded-2xl p-6 shadow-lg">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20 flex items-center justify-center mb-4">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3">Visi</h3>
                        <p class="text-sm text-white/95 leading-relaxed">{{ $extracurricular->vision }}</p>
                    </div>
                @endif

                @if ($extracurricular->mission)
                    <div class="bg-gradient-to-br from-primary-700 to-primary-900 text-white rounded-2xl p-6 shadow-lg">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20 flex items-center justify-center mb-4">
                            <i data-lucide="target" class="w-5 h-5"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-3">Misi</h3>
                        <ol class="space-y-2">
                            @foreach (array_filter(array_map('trim', explode("\n", $extracurricular->mission))) as $i => $m)
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
        @if ($extracurricular->gallery && count($extracurricular->gallery))
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
                <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                    <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                    Galeri Kegiatan
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach ($extracurricular->gallery as $img)
                        <img src="{{ asset('storage/' . $img) }}"
                             class="aspect-video w-full object-cover rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Ekstrakurikuler Lain --}}
        @if ($others->isNotEmpty())
            <div>
                <h2 class="text-xl font-bold text-surface-900 mb-5 flex items-center gap-2">
                    <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                    Ekstrakurikuler Lainnya
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($others as $o)
                        <a href="{{ route('public.kesiswaan.ekstrakurikuler.show', $o->slug ?? $o->id) }}"
                           class="group bg-white rounded-xl border border-surface-200/70 overflow-hidden shadow-sm hover:shadow-lg transition-all hover:-translate-y-1">
                            <div class="aspect-square bg-surface-100 overflow-hidden">
                                @if ($o->image)
                                    <img src="{{ asset('storage/' . $o->image) }}" alt="{{ $o->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                        <i data-lucide="activity" class="w-8 h-8 text-primary-400"></i>
                                    </div>
                                @endif
                            </div>
                            <p class="p-3 text-xs font-semibold text-surface-800 text-center truncate">{{ $o->name }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- CTA --}}
        <div class="text-center pt-4">
            <a href="{{ route('public.kesiswaan.ekstrakurikuler.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full border-2 border-primary-500
                      text-primary-600 hover:bg-primary-600 hover:text-white text-sm font-semibold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Semua Ekstrakurikuler
            </a>
        </div>
    </section>
</div>

@endsection