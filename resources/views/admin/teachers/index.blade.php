@extends('layouts.backend')

@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')

    <x-ui.page-header title="Data Guru" subtitle="Kelola data guru dan staf sekolah">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.teachers.create') }}" icon="plus">Tambah Guru</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, NIP, atau jabatan..."
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

        @if ($teachers->isEmpty())
            <x-ui.empty-state icon="user-check" title="Belum ada guru">
                <x-slot:actions>
                    <x-ui.button href="{{ route('admin.teachers.create') }}" icon="plus" size="sm">Tambah Guru</x-ui.button>
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
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">NIP</th>
                            <th class="px-4 py-3 font-medium">Jabatan</th>
                            <th class="px-4 py-3 font-medium">Kontak</th>
                            <th class="px-4 py-3 font-medium w-28">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($teachers as $i => $teacher)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $teacher->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $teachers->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($teacher->photo)
                                            <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->name }}"
                                                 class="w-9 h-9 rounded-full object-cover shrink-0 ring-2 ring-surface-100">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600
                                                        text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                                {{ strtoupper(substr($teacher->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <p class="font-medium text-surface-800">{{ $teacher->name }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-surface-600">{{ $teacher->nip ?: '—' }}</td>
                                <td class="px-4 py-3 text-surface-600">{{ $teacher->position ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($teacher->email) <p class="text-xs text-surface-500">{{ $teacher->email }}</p> @endif
                                    @if ($teacher->phone) <p class="text-xs text-surface-500">{{ $teacher->phone }}</p> @endif
                                    @if (!$teacher->email && !$teacher->phone) <span class="text-surface-400">—</span> @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if ($teacher->is_active)
                                        <x-ui.badge color="green" :dot="true">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge color="red" :dot="true">Tidak Aktif</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('admin.teachers.edit', $teacher)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $teacher->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($teachers->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $teachers->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Guru"
        message="Apakah Anda yakin ingin menghapus data guru ini?" confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('admin.teachers.bulk-action')"
    entity="guru"
    :actions="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
    :confirm="true" />
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/teachers/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush