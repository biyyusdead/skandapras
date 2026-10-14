@extends('layouts.backend')

@section('title', 'PKL & BKK')
@section('page-title', 'PKL & BKK')

@section('content')

    <x-ui.page-header title="PKL & BKK" subtitle="Kelola informasi PKL dan Bursa Kerja Khusus">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.internships.create') }}" icon="plus">Tambah Data</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau perusahaan..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                             transition-all duration-150 hover:border-surface-400
                                             focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="1" @selected(request('status') === '1')>Aktif</option>
                    <option value="0" @selected(request('status') === '0')>Tidak Aktif</option>
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($internships->isEmpty())
            <x-ui.empty-state icon="briefcase" title="Belum ada data PKL & BKK">
                <x-slot:actions>
                    <x-ui.button href="{{ route('admin.internships.create') }}" icon="plus" size="sm">Tambah Data</x-ui.button>
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 w-12">
                                <input type="checkbox"
                                    data-bulk-select-all
                                    onchange="toggleAll(this)"
                                    class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                            </th>
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Judul</th>
                            <th class="px-4 py-3 font-medium">Perusahaan</th>
                            <th class="px-4 py-3 font-medium">Periode</th>
                            <th class="px-4 py-3 font-medium w-28">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($internships as $i => $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $item->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $internships->firstItem() + $i }}</td>
                                <td class="px-4 py-3 font-medium text-surface-800">{{ $item->title }}</td>
                                <td class="px-4 py-3 text-surface-600">{{ $item->company ?: '—' }}</td>
                                <td class="px-4 py-3 text-surface-600 text-xs">
                                    @if ($item->start_date || $item->end_date)
                                        {{ $item->start_date?->format('d M Y') ?? '?' }}
                                        –
                                        {{ $item->end_date?->format('d M Y') ?? '?' }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->is_active)
                                        <x-ui.badge color="green" :dot="true">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="red" :dot="true">Tidak Aktif</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('admin.internships.edit', $item)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $item->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($internships->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $internships->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Data"
        message="Apakah Anda yakin ingin menghapus data ini?" confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('admin.internships.bulk-action')"
    entity="data"
    :actions="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
    :confirm="true" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/internships/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush