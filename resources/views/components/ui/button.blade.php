@props([
    'variant' => 'primary',
    'size'    => 'md',
    'type'    => 'button',
    'icon'    => null,
    'href'    => null,
    'loading' => false,
    'loadingText' => 'Memproses...',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-lg
             transition-all duration-200 ease-smooth
             focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
             disabled:opacity-60 disabled:cursor-not-allowed active:scale-[0.98]';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-3.5 py-2 text-sm',
        'lg' => 'px-5 py-2.5 text-sm',
    ];

    $variants = [
        'primary'   => 'bg-primary-600 hover:bg-primary-700 text-white shadow-sm hover:shadow-md focus-visible:ring-primary-500',
        'secondary' => 'bg-surface-100 hover:bg-surface-200 text-surface-700 focus-visible:ring-surface-400',
        'danger'    => 'bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-md focus-visible:ring-red-500',
        'ghost'     => 'hover:bg-surface-100 text-surface-600 focus-visible:ring-surface-300',
        'outline'   => 'border border-surface-300 hover:border-primary-400 hover:bg-primary-50/50 text-surface-700 focus-visible:ring-primary-400',
    ];

    $classes = "{$base} {$sizes[$size]} {$variants[$variant]}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        @endif
        {{ $slot }}
    </a>
@elseif ($loading)
    {{-- Tombol submit dengan loading state, listen ke event 'submit' dari form induk --}}
    <button
        type="{{ $type }}"
        x-data="{ loading: false }"
        x-on:form-submit.window="loading = true"
        x-on:pageshow.window="loading = false"
        :disabled="loading"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        <template x-if="!loading">
            <span class="inline-flex items-center gap-2">
                @if ($icon)
                    <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                @endif
                {{ $slot }}
            </span>
        </template>
        <template x-if="loading">
            <span class="inline-flex items-center gap-2">
                <x-ui.spinner size="sm" />
                <span>{{ $loadingText }}</span>
            </span>
        </template>
    </button>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
        @endif
        {{ $slot }}
    </button>
@endif