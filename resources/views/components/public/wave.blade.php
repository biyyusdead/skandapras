@props([
    'color'  => '#FAFAF7',   // warna wave (default: surface-50)
    'height' => 'md',         // sm | md | lg
])

@php
    $heightClass = match($height) {
        'sm' => 'h-8 md:h-12 lg:h-14',
        'lg' => 'h-20 md:h-28 lg:h-32',
        default => 'h-12 md:h-16 lg:h-20', // md
    };
@endphp

{{-- 🌊 Wave Bottom — Seamless Single Layer --}}
<div {{ $attributes->merge(['class' => 'absolute bottom-0 left-0 right-0 leading-none pointer-events-none overflow-hidden']) }}>
    <div class="relative w-full {{ $heightClass }}">

        <svg viewBox="0 0 1200 80"
             preserveAspectRatio="none"
             xmlns="http://www.w3.org/2000/svg"
             class="wave-svg">

            <path d="M0,50
                     C100,40 150,55 250,50
                     C350,45 400,35 500,45
                     C600,55 650,60 750,50
                     C850,40 900,35 1000,45
                     C1100,55 1150,50 1200,50
                     L1200,80 L0,80 Z
                     M1200,50
                     C1300,40 1350,55 1450,50
                     C1550,45 1600,35 1700,45
                     C1800,55 1850,60 1950,50
                     C2050,40 2100,35 2200,45
                     C2300,55 2350,50 2400,50
                     L2400,80 L1200,80 Z"
                  fill="{{ $color }}"/>

        </svg>
    </div>
</div>