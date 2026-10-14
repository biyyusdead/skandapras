@props([
    'label',
    'value',
    'icon'  => 'activity',
    'color' => 'primary',
    'trend' => null, // opsional, misal "+12%"
])

@php
    $colors = [
        'primary' => ['bg' => 'bg-primary-50', 'text' => 'text-primary-600', 'ring' => 'ring-primary-100'],
        'green'   => ['bg' => 'bg-green-50',   'text' => 'text-green-600',   'ring' => 'ring-green-100'],
        'yellow'  => ['bg' => 'bg-yellow-50',  'text' => 'text-yellow-600',  'ring' => 'ring-yellow-100'],
        'red'     => ['bg' => 'bg-red-50',     'text' => 'text-red-600',     'ring' => 'ring-red-100'],
        'blue'    => ['bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'ring' => 'ring-blue-100'],
        'indigo'  => ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'ring' => 'ring-indigo-100'],
    ];
    $c = $colors[$color] ?? $colors['primary'];
@endphp

<div class="group bg-white border border-surface-200/70 rounded-card shadow-card p-5 hover-lift">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-surface-500 uppercase tracking-wide">{{ $label }}</p>
            <p class="text-2xl font-bold text-surface-900 mt-1.5 tabular-nums">{{ $value }}</p>
            @if ($trend)
                <p class="text-xs text-surface-400 mt-1">{{ $trend }}</p>
            @endif
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                    {{ $c['bg'] }} {{ $c['text'] }} ring-1 {{ $c['ring'] }}
                    transition-transform duration-300 ease-smooth group-hover:scale-110">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
    </div>
</div>