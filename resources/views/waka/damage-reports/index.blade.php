@extends('layouts.backend')

@section('title', 'Laporan Kerusakan')
@section('page-title', 'Laporan Kerusakan')

@section('content')

    <x-ui.page-header title="Laporan Kerusakan" subtitle="Kelola laporan kerusakan dari pengguna ruangan" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Laporan" :value="$stats['total']" icon="file-text" color="primary" />
        <x-ui.stat-card label="Menunggu Verifikasi" :value="$stats['menunggu']" icon="clock" color="yellow" />
        <x-ui.stat-card label="Dalam Proses" :value="$stats['diproses']" icon="loader" color="indigo" />
        <x-ui.stat-card label="Selesai" :value="$stats['selesai']" icon="check-circle" color="green" />
    </div>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                <div class="relative lg:col-span-2">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau deskripsi..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Status</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->value }}" @selected(request('status') === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <select name="room_id" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Ruangan</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                    @endforeach
                </select>
                <div class="lg:col-span-4">
                    <x-ui.button type="submit" variant="secondary" icon="filter" size="sm">Filter</x-ui.button>
                    @if (request()->hasAny(['search', 'status', 'room_id']))
                        <x-ui.button href="{{ route('waka.damage-reports.index') }}" variant="ghost" icon="x" size="sm">Reset</x-ui.button>
                    @endif
                </div>
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
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-medium text-surface-800">{{ $report->displayName() }}</span>
                                        <span class="text-[10px] text-surface-500">{{ $report->reporter_type?->label() ?? 'Pengunjung' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $report->reported_at?->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1 items-start">
                                        <x-ui.badge :color="$report->status->color()" :dot="true" class="!text-[11px]">{{ $report->status->label() }}</x-ui.badge>
                                        @if ($report->identity_status)
                                            <span class="text-[10px] font-medium {{ $report->identity_status === \App\Enums\IdentityStatus::TERVERIFIKASI ? 'text-green-600' : 'text-amber-600' }}">
                                                {{ $report->identity_status->label() }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('waka.damage-reports.show', $report)" icon="eye" title="Detail & Verifikasi" />
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