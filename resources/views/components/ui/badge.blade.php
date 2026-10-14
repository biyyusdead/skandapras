@props([
    'color' => 'gray',
    'icon'  => null,
    'dot'   => false,
])

@php
    $colors = [
        'gray'   => 'bg-surface-100 text-surface-700 ring-surface-200',
        'green'  => 'bg-green-50 text-green-700 ring-green-200/60',
        'yellow' => 'bg-yellow-50 text-yellow-700 ring-yellow-200/60',
        'red'    => 'bg-red-50 text-red-700 ring-red-200/60',
        'purple' => 'bg-primary-50 text-primary-700 ring-primary-200/60',
        'blue'   => 'bg-blue-50 text-blue-700 ring-blue-200/60',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-200/60',
    ];
    $c = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-0.5 text-xs font-medium rounded-full ring-1 ring-inset {$c}"]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
    @elseif ($icon)
        <i data-lucide="{{ $icon }}" class="w-3 h-3"></i>
    @endif
    {{ $slot }}
</span>