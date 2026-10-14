@props([
    'headers' => [],   // array label kolom
    'striped' => false,
])

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80
                       border-b border-surface-200/70">
                @foreach ($headers as $header)
                    <th class="px-4 py-3 font-medium whitespace-nowrap">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-100">
            {{ $slot }}
        </tbody>
    </table>
</div>