@props([
    'size' => 'md',  // sm | md | lg
])

@php
    $sizes = [
        'sm' => 'w-3.5 h-3.5 border-2',
        'md' => 'w-4 h-4 border-2',
        'lg' => 'w-6 h-6 border-[3px]',
    ];
@endphp

<span
    {{ $attributes->merge(['class' => "inline-block animate-spin rounded-full border-current border-t-transparent {$sizes[$size]}"]) }}
    role="status"
    aria-label="Memuat"
></span>