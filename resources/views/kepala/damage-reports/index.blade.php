@extends('layouts.backend')

@section('title', 'Laporan Kerusakan')
@section('page-title', 'Laporan Kerusakan')

@section('content')

    <x-ui.page-header title="Laporan Kerusakan"
        subtitle="Monitoring seluruh laporan kerusakan sarana prasarana" />

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode laporan..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                             transition-all duration-150 hover:border-surface-400
                                             focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($reports->isEmpty())
            <x-ui.empty-state icon="file-text" title="Belum ada laporan" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Objek</th>
                            <th class="px-4 py-3 font-medium">Pelapor</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium w-40">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-16">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($reports as $i => $report)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $reports->firstItem() + $i }}</td>
                                <td class="px-4 py-3"><span class="text-xs font-mono text-surface-700">{{ $report->report_code }}</span></td>
                                <td class="px-4 py-3 text-surface-700">{{ $report->room->name }}</td>
                                <td class="px-4 py-3 text-surface-600">{{ $report->itemRoom?->item?->name ?? 'Ruangan (umum)' }}</td>
                                <td class="px-4 py-3 text-surface-600 text-xs">{{ $report->reporter?->name ?? 'Pengunjung' }}</td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $report->reported_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end">
                                        <x-ui.table-action :href="route('kepala.damage-reports.show', $report)" icon="eye" title="Detail" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($reports->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $reports->links() }}</div>
            @endif
        @endif
    </x-ui.card>

@endsection