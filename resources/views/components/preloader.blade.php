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

<style>
    /* ═══ WRAPPER ═══ */
    .skandapras-preloader {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.35s ease-out, visibility 0.35s ease-out;
        overflow: hidden;
        pointer-events: auto;
    }

    .skandapras-preloader.is-hidden {
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        display: none !important;
    }

    .skandapras-preloader.is-opening {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
    }

    /* ═══ OVERLAY ═══ */
    .preloader-overlay {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: #FAFAF7;
        transition: opacity 0.3s ease-out;
    }

    /* ═══ KONTEN ═══ */
    .preloader-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1.25rem;
        padding: 1.5rem;
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
    }

    .skandapras-preloader.is-opening .preloader-content {
        opacity: 0;
        transform: scale(0.96);
    }

    /* ═══ LOGO WRAPPER ═══ */
    .preloader-brand-wrapper {
        position: relative;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .preloader-logo {
        position: relative;
        z-index: 2;
        width: 64px;
        height: 64px;
        filter: drop-shadow(0 8px 16px rgba(108, 74, 182, 0.25));
    }

    .preloader-glow {
        position: absolute;
        inset: -6px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(108, 74, 182, 0.25) 0%, rgba(245, 158, 11, 0.15) 60%, transparent 100%);
        filter: blur(8px);
        z-index: 1;
        animation: preloaderPulse 2s ease-in-out infinite;
    }

    @keyframes preloaderPulse {
        0%, 100% { transform: scale(0.95); opacity: 0.6; }
        50%      { transform: scale(1.15); opacity: 1; }
    }

    .preloader-text {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        text-align: center;
    }

    @media (prefers-reduced-motion: reduce) {
        .skandapras-preloader {
            display: none !important;
        }
    }
</style>

<script>
    (function() {
        const el = document.getElementById('skandapras-preloader');
        if (!el) return;

        // Jika user pernah buka atau prefer-reduced-motion, langsung tutup
        if (sessionStorage.getItem('skandapras_preloader_shown') === '1' ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            el.classList.add('is-hidden');
            return;
        }

        let closed = false;
        function dismissPreloader() {
            if (closed) return;
            closed = true;
            el.classList.add('is-opening');
            setTimeout(function() {
                el.classList.add('is-hidden');
                sessionStorage.setItem('skandapras_preloader_shown', '1');
            }, 300);
        }

        // Buka setelah DOM ready dengan delay minimal untuk efek smooth (maks 250ms)
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(dismissPreloader, 150);
        } else {
            window.addEventListener('DOMContentLoaded', function() {
                setTimeout(dismissPreloader, 200);
            }, { once: true });
        }

        // Safety fallback: maksimal 1 detik
        setTimeout(dismissPreloader, 1000);
    })();
</script>