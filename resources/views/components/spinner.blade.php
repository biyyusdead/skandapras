@props([
    'size' => 'md',      // xs | sm | md | lg | xl
    'variant' => 'primary', // primary | white | surface
    'label' => 'Memuat...',
    'inline' => false,
])

@php
    $sizeMap = [
        'xs' => 'w-3 h-3 border',
        'sm' => 'w-4 h-4 border-2',
        'md' => 'w-6 h-6 border-2',
        'lg' => 'w-8 h-8 border-[3px]',
        'xl' => 'w-12 h-12 border-4',
    ];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['md'];

    $variantMap = [
        'primary' => 'border-primary-500 border-t-transparent',
        'white'   => 'border-white/40 border-t-white',
        'surface' => 'border-surface-300 border-t-surface-600',
    ];
    $variantClass = $variantMap[$variant] ?? $variantMap['primary'];
@endphp

<span
    @if(!$inline) role="status" aria-label="{{ $label }}" @endif
    {{ $attributes->merge([
        'class' => "inline-block rounded-full animate-spin $sizeClass $variantClass"
    ]) }}
></span>