@extends('layouts.backend')

@section('title', 'Data Alumni')
@section('page-title', 'Data Alumni')

@section('content')

    <x-ui.page-header title="Data Alumni" subtitle="Kelola data lulusan terbaik dan alumni">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.alumni.create') }}" icon="plus">Tambah Alumni</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, jurusan, prestasi, motto..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="year" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                           transition-all duration-150 hover:border-surface-400
                                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                                           min-w-[180px]">
                    <option value="">Semua Tahun Lulus</option>
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected((string) request('year') === (string) $y)>{{ $y }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($alumni->isEmpty())
            <x-ui.empty-state icon="users" title="Belum ada data alumni">
                <x-slot:actions>
                    <x-ui.button href="{{ route('admin.alumni.create') }}" icon="plus" size="sm">Tambah Alumni</x-ui.button>
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
                            <th class="px-4 py-3 font-medium w-20">Foto</th>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium w-24">Tahun Lulus</th>
                            <th class="px-4 py-3 font-medium">Jurusan</th>
                            <th class="px-4 py-3 font-medium">Prestasi</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($alumni as $i => $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $item->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $alumni->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}" alt="{{ $item->name }}"
                                             class="w-12 h-12 rounded-full object-cover ring-1 ring-surface-200">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-surface-100 flex items-center justify-center">
                                            <i data-lucide="user" class="w-5 h-5 text-surface-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-800">{{ $item->name }}</p>
                                    @if ($item->motto)
                                        <p class="text-xs text-surface-400 italic truncate max-w-md">"{{ Str::limit($item->motto, 60) }}"</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-surface-600 tabular-nums">{{ $item->graduation_year ?? '—' }}</td>
                                <td class="px-4 py-3 text-surface-600">{{ $item->department ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->achievement)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                                     bg-primary-50 text-primary-700 text-xs font-medium">
                                            <i data-lucide="trophy" class="w-3 h-3"></i>
                                            {{ $item->achievement }}
                                        </span>
                                    @else
                                        <span class="text-surface-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('admin.alumni.edit', $item)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $item->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($alumni->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $alumni->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Alumni"
        message="Apakah Anda yakin ingin menghapus data alumni ini? Tindakan ini tidak dapat dibatalkan."
        confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
        :action="route('admin.alumni.bulk-action')"
        entity="alumni"
        :actions="['delete' => 'Hapus']"
        :confirm="true" />
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/alumni/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush