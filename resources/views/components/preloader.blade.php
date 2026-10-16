{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- PRELOADER — Branded Lightweight Transition                 --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
@php
    $schoolLogo = \App\Models\SchoolProfile::current()?->logo;
@endphp

<div id="skandapras-preloader" class="skandapras-preloader" aria-hidden="true">
    {{-- Overlay putih transparan halus --}}
    <div class="preloader-overlay"></div>

    {{-- Content --}}
    <div class="preloader-content">
        <div class="preloader-brand-wrapper">
            @if ($schoolLogo)
                <img src="{{ asset('storage/' . $schoolLogo) }}"
                     alt="Logo SKANDAPRAS"
                     width="72" height="72"
                     fetchpriority="high"
                     class="preloader-logo object-contain">
            @else
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-600 to-primary-800 flex items-center justify-center text-white font-bold text-2xl shadow-lg">
                    S2
                </div>
            @endif

            {{-- Ring glow animatif --}}
            <div class="preloader-glow"></div>
        </div>

        {{-- Tulisan Memuat --}}
        <div class="preloader-text">
            <span class="font-bold text-primary-900 tracking-tight text-base sm:text-lg">SMK Negeri 2 Karanganyar</span>
            <div class="flex items-center gap-1.5 text-xs text-primary-600 font-medium">
                <span>Memuat</span>
                <span class="inline-flex gap-0.5 preloader-dots">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-primary-500 animate-pulse delay-75"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-accent-amber animate-pulse delay-150"></span>
                </span>
            </div>
        </div>
    </div>
</div>