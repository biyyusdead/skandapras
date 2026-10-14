@props([
    'title',
    'subtitle' => null,
    'actions'  => null,
])

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fade-in-up">
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-surface-900 tracking-tight">{{ $title }}</h1>
        @if ($subtitle)
            <p class="text-sm text-surface-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>

    @if ($actions)
        <div class="flex items-center gap-2 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>