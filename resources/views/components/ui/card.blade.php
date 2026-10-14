@props([
    'padding' => true,
    'title'   => null,
    'subtitle'=> null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-surface-200/70 rounded-card shadow-card']) }}>
    @if ($title)
        <div class="px-5 py-4 border-b border-surface-100">
            <h3 class="font-semibold text-surface-900 text-[15px]">{{ $title }}</h3>
            @if ($subtitle)
                <p class="text-xs text-surface-500 mt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</div>