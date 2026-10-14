@extends('layouts.backend')

@section('title', 'Persetujuan')
@section('page-title', 'Persetujuan Tahap 1')

@section('content')

    <x-ui.page-header title="Persetujuan Tahap 1"
        subtitle="Laporan yang membutuhkan verifikasi atau persetujuan Anda" />

    {{-- Pending --}}
    <x-ui.card :padding="false" title="Menunggu Tindakan" subtitle="Klik laporan untuk verifikasi atau setujui">
        @if ($pending->isEmpty())
            <div class="p-5">
                <x-ui.empty-state icon="check-circle" title="Tidak ada laporan yang menunggu tindakan"
                    description="Semua laporan sudah ditangani." />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Pelapor</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium w-40">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($pending as $report)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 font-mono text-xs text-surface-700">{{ $report->report_code }}</td>
                                <td class="px-4 py-3 text-surface-700">{{ $report->room->name }}</td>
                                <td class="px-4 py-3 text-surface-600 text-xs">{{ $report->reporter?->name ?? 'Pengunjung' }}</td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $report->reported_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3"><x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge></td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.table-action :href="route('waka.damage-reports.show', $report)" icon="arrow-right" title="Tangani" />
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

    {{-- History --}}
    <x-ui.card :padding="false" title="Riwayat Persetujuan Anda" class="mt-5">
        @if ($history->isEmpty())
            <div class="p-5">
                <x-ui.empty-state icon="scroll-text" title="Belum ada riwayat persetujuan" />
            </div>
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
                                <td class="px-4 py-3">
                                    <a href="{{ route('waka.damage-reports.show', $item->damageReport) }}"
                                       class="font-mono text-xs text-primary-600 hover:underline">
                                        {{ $item->damageReport->report_code }}
                                    </a>
                                </td>
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