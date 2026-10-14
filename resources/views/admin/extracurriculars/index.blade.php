@extends('layouts.backend')

@section('title', 'Data Ekstrakurikuler')
@section('page-title', 'Data Ekstrakurikuler')

@section('content')

    <x-ui.page-header title="Data Ekstrakurikuler" subtitle="Kelola kegiatan ekstrakurikuler sekolah">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.extracurriculars.create') }}" icon="plus">Tambah Ekskul</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ekstrakurikuler..."
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

        @if ($extracurriculars->isEmpty())
            <x-ui.empty-state icon="activity" title="Belum ada ekstrakurikuler">
                <x-slot:actions>
                    <x-ui.button href="{{ route('admin.extracurriculars.create') }}" icon="plus" size="sm">Tambah Ekskul</x-ui.button>
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
                            <th class="px-4 py-3 font-medium w-20">Gambar</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="px-4 py-3 font-medium w-28">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($extracurriculars as $i => $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $item->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $extracurriculars->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" class="w-14 h-10 rounded-lg object-cover ring-1 ring-surface-200">
                                    @else
                                        <div class="w-14 h-10 rounded-lg bg-surface-100 flex items-center justify-center">
                                            <i data-lucide="image" class="w-4 h-4 text-surface-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-surface-800">{{ $item->name }}</td>
                                <td class="px-4 py-3 text-surface-600 max-w-md truncate">{{ $item->description ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->is_active)
                                        <x-ui.badge color="green" :dot="true">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="red" :dot="true">Tidak Aktif</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('admin.extracurriculars.edit', $item)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $item->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($extracurriculars->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $extracurriculars->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Ekstrakurikuler"
        message="Apakah Anda yakin ingin menghapus ekstrakurikuler ini?" confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('admin.extracurriculars.bulk-action')"
    entity="ekstrakurikuler"
    :actions="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
    :confirm="true" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/extracurriculars/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush