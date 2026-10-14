@props([
    'type' => 'text',   // text | line | card | table | list | avatar | stat | form
    'count' => 1,
    'rows' => 5,        // khusus table
    'cols' => 4,        // khusus table
])

@php
    $shimmer = 'animate-shimmer rounded-md';
    $block   = 'animate-shimmer rounded-xl';
@endphp

@switch($type)

    {{-- ============ TEXT: baris teks pendek ============ --}}
    @case('text')
        @for ($i = 0; $i < $count; $i++)
            <div class="h-4 w-full max-w-md {{ $shimmer }} mb-2"></div>
        @endfor
        @break

    {{-- ============ LINE: 1 baris dengan lebar custom ============ --}}
    @case('line')
    <div {{ $attributes->merge(['class' => "h-4 {$shimmer}"]) }}></div>
    @break

    {{-- ============ AVATAR: bulat ============ --}}
    @case('avatar')
        <div class="w-10 h-10 rounded-full {{ $block }}"></div>
        @break

    {{-- ============ CARD: kotak seperti kartu statistik ============ --}}
    @case('card')
        @for ($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-xl border border-surface-200/70 p-5 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg {{ $block }}"></div>
                    <div class="w-12 h-4 {{ $shimmer }}"></div>
                </div>
                <div class="h-6 w-24 {{ $shimmer }} mb-2"></div>
                <div class="h-3 w-32 {{ $shimmer }}"></div>
            </div>
        @endfor
        @break

    {{-- ============ STAT: angka statistik besar ============ --}}
    @case('stat')
        @for ($i = 0; $i < $count; $i++)
            <div class="bg-white rounded-xl border border-surface-200/70 p-5">
                <div class="h-3 w-20 {{ $shimmer }} mb-3"></div>
                <div class="h-8 w-28 {{ $block }} mb-2"></div>
                <div class="h-3 w-24 {{ $shimmer }}"></div>
            </div>
        @endfor
        @break

    {{-- ============ LIST: item list dengan avatar ============ --}}
    @case('list')
        @for ($i = 0; $i < $count; $i++)
            <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-surface-200/70 mb-2">
                <div class="w-10 h-10 rounded-full {{ $block }} shrink-0"></div>
                <div class="flex-1 min-w-0">
                    <div class="h-4 w-1/3 {{ $shimmer }} mb-2"></div>
                    <div class="h-3 w-2/3 {{ $shimmer }}"></div>
                </div>
                <div class="w-16 h-6 {{ $shimmer }} shrink-0"></div>
            </div>
        @endfor
        @break

    {{-- ============ TABLE: header + baris ============ --}}
    @case('table')
        <div class="bg-white rounded-xl border border-surface-200/70 overflow-hidden">
            {{-- Header --}}
            <div class="bg-surface-50/60 px-4 py-3 border-b border-surface-200/70 flex gap-3">
                @for ($c = 0; $c < $cols; $c++)
                    <div class="h-4 flex-1 {{ $shimmer }}"></div>
                @endfor
            </div>
            {{-- Rows --}}
            @for ($r = 0; $r < $rows; $r++)
                <div class="px-4 py-3 border-b border-surface-100 flex gap-3 items-center">
                    @for ($c = 0; $c < $cols; $c++)
                        <div class="h-4 flex-1 {{ $shimmer }}" style="opacity: {{ 1 - ($r * 0.08) }}"></div>
                    @endfor
                </div>
            @endfor
        </div>
        @break

    {{-- ============ FORM: label + input ============ --}}
    @case('form')
        @for ($i = 0; $i < $count; $i++)
            <div class="mb-4">
                <div class="h-3 w-24 {{ $shimmer }} mb-2"></div>
                <div class="h-10 w-full {{ $block }}"></div>
            </div>
        @endfor
        @break

@endswitch