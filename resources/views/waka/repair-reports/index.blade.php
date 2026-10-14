@extends('layouts.backend')

@section('title', 'Laporan Perbaikan')
@section('page-title', 'Laporan Perbaikan')

@section('content')

    <x-ui.page-header title="Laporan Perbaikan" subtitle="Kelola proses perbaikan sarana prasarana" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Perbaikan" :value="$stats['total']" icon="wrench" color="primary" />
        <x-ui.stat-card label="Siap" :value="$stats['ready']" icon="package" color="yellow" />
        <x-ui.stat-card label="Diproses" :value="$stats['in_progress']" icon="loader" color="indigo" />
        <x-ui.stat-card label="Selesai" :value="$stats['completed']" icon="check-circle" color="green" />
    </div>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari teknisi atau kode laporan..."
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
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($repairs->isEmpty())
            <x-ui.empty-state icon="wrench" title="Belum ada data perbaikan"
                description="Data perbaikan muncul setelah laporan disetujui kedua tahap." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Kode Laporan</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Teknisi</th>
                            <th class="px-4 py-3 font-medium">Biaya</th>
                            <th class="px-4 py-3 font-medium w-32">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($repairs as $i => $repair)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $repairs->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('waka.damage-reports.show', $repair->damageReport) }}"
                                       class="text-xs font-mono text-primary-600 hover:underline">
                                        {{ $repair->damageReport->report_code }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-surface-700">{{ $repair->damageReport->room->name }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-surface-800">{{ $repair->technician_name }}</p>
                                    @if ($repair->technician_contact)
                                        <p class="text-xs text-surface-400">{{ $repair->technician_contact }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-surface-700 tabular-nums">
                                    {{ $repair->repair_cost ? 'Rp ' . number_format($repair->repair_cost, 0, ',', '.') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge :color="$repair->status->color()" :dot="true">{{ $repair->status->label() }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('waka.repair-reports.edit', $repair)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $repair->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($repairs->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $repairs->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Data Perbaikan"
        message="Apakah Anda yakin ingin menghapus data perbaikan ini? Status laporan akan dikembalikan."
        confirm-text="Ya, Hapus" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/waka/repair-reports/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush