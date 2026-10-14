@props([
    'icon'  => 'inbox',
    'title' => 'Tidak ada data',
    'description' => null,
])

<div class="flex flex-col items-center justify-center py-14 text-center animate-fade-in">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-surface-50 to-surface-100 ring-1 ring-surface-200/60 flex items-center justify-center mb-4">
        <i data-lucide="{{ $icon }}" class="w-7 h-7 text-surface-400"></i>
    </div>
    <p class="font-medium text-surface-700">{{ $title }}</p>
    @if ($description)
        <p class="text-sm text-surface-500 mt-1 max-w-sm">{{ $description }}</p>
    @endif
    @if (isset($actions))
        <div class="mt-5">{{ $actions }}</div>
    @endif
</div>