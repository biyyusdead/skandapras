@extends('layouts.backend')

@section('title', 'Laporan Saya')
@section('page-title', 'Laporan Saya')

@section('content')

    <x-ui.page-header title="Laporan Saya" subtitle="Riwayat laporan kerusakan yang Anda buat">
        <x-slot:actions>
            <x-ui.button href="{{ route('pengguna.damage-reports.create') }}" icon="plus">
                Buat Laporan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau deskripsi..."
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
            <x-ui.empty-state icon="file-text" title="Belum ada laporan"
                description="Buat laporan jika menemukan kerusakan pada ruangan Anda.">
                <x-slot:actions>
                    <x-ui.button href="{{ route('pengguna.damage-reports.create') }}" icon="plus" size="sm">
                        Buat Laporan
                    </x-ui.button>
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Kode</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Objek</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium w-36">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($reports as $i => $report)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $reports->firstItem() + $i }}</td>
                                <td class="px-4 py-3"><span class="text-xs font-mono text-surface-700">{{ $report->report_code }}</span></td>
                                <td class="px-4 py-3 text-surface-700">{{ $report->room->name }}</td>
                                <td class="px-4 py-3 text-surface-600">{{ $report->itemRoom?->item?->name ?? 'Ruangan (umum)' }}</td>
                                <td class="px-4 py-3 text-surface-500 text-xs">{{ $report->reported_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3"><x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('pengguna.damage-reports.show', $report)" icon="eye" title="Detail" />
                                        @if ($report->status->value === 'menunggu_verifikasi')
                                            <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                                :onclick="'confirmDelete(' . $report->id . ')'" />
                                        @endif
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

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Laporan"
        message="Apakah Anda yakin ingin menghapus laporan ini?" confirm-text="Ya, Hapus" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/pengguna/reports/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush