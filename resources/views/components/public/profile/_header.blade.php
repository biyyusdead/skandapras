@props(['title', 'subtitle' => null, 'active' => 'index'])

<section class="relative overflow-hidden text-white">

    {{-- Background image --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             alt="Foto Sekolah"
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/85 via-primary-800/80 to-primary-700/75"></div>
    </div>

    {{-- Decorative blobs --}}
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Decorative pattern --}}
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 32px 32px;"></div>

    {{-- Content --}}
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-20 md:pb-24 text-center">

        {{-- Breadcrumb — centered --}}
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5 mt-2
                    animate-fade-in-up flex-wrap">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                <i data-lucide="home" class="w-3 h-3"></i>
                Beranda
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <a href="{{ route('public.profile.index') }}" class="hover:text-white transition-colors">
                Tentang
            </a>
            @if ($active !== 'index')
                <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
                <span class="text-white font-medium">{{ $title }}</span>
            @endif
        </nav>

        {{-- Title — dengan accent kuning di kata terakhir --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight
                   animate-fade-in-up stagger-2">
            @php
                $words = explode(' ', $title);
                $lastWord = array_pop($words);
                $firstPart = implode(' ', $words);
            @endphp

            @if ($firstPart)
                {{ $firstPart }}
            @endif
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                {{ $lastWord }}
            </span>
        </h1>

        {{-- Subtitle --}}
        @if ($subtitle)
            <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90
                      animate-fade-in-up stagger-3">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    {{-- 🌊 Wave Bottom via komponen --}}
    <x-public.wave />

</section>