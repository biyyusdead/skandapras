@extends('layouts.backend')

@section('title', 'Persetujuan 2')
@section('page-title', 'Persetujuan Tahap 2')

@section('content')

    <x-ui.page-header title="Persetujuan Tahap 2"
        subtitle="Laporan yang menunggu persetujuan Kepala Sekolah" />

    <x-ui.card :padding="false" title="Menunggu Persetujuan">
        @if ($pending->isEmpty())
            <div class="p-5">
                <x-ui.empty-state icon="check-circle" title="Tidak ada yang menunggu persetujuan"
                    description="Semua laporan sudah ditangani." />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium">Pelapor</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($pending as $report)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 font-mono text-xs text-surface-700">{{ $report->report_code }}</td>
                                <td class="px-4 py-3 text-surface-700">{{ $report->room->name }}</td>
                                <td class="px-4 py-3">
                                    @if ($report->damage_category)
                                        <x-ui.badge :color="$report->damage_category->color()" :dot="true">{{ $report->damage_category->label() }}</x-ui.badge>
                                    @else
                                        <span class="text-xs text-surface-500">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-surface-600 text-xs">
                                    <div class="font-medium text-surface-800">{{ $report->displayName() }}</div>
                                    <div class="text-[11px] text-surface-500">{{ $report->reporter_type?->label() ?? 'Umum' }}</div>
                                </td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $report->reported_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.table-action :href="route('kepala.damage-reports.show', $report)" icon="arrow-right" title="Review" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($pending->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $pending->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <x-ui.card :padding="false" title="Riwayat Persetujuan" class="mt-5">
        @if ($history->isEmpty())
            <div class="p-5"><x-ui.empty-state icon="scroll-text" title="Belum ada riwayat" /></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium">Kode Laporan</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Keputusan</th>
                            <th class="px-4 py-3 font-medium">Catatan</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($history as $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 font-mono text-xs text-primary-600">{{ $item->damageReport->report_code }}</td>
                                <td class="px-4 py-3 text-surface-700">{{ $item->damageReport->room->name }}</td>
                                <td class="px-4 py-3"><x-ui.badge :color="$item->status->color()" :dot="true">{{ $item->status->label() }}</x-ui.badge></td>
                                <td class="px-4 py-3 text-surface-600 text-xs max-w-md truncate">{{ $item->notes ?: '—' }}</td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $item->approved_at?->format('d M Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($history->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $history->links() }}</div>
            @endif
        @endif
    </x-ui.card>

@endsection