@extends('layouts.backend')

@section('title', 'Data Kategori')
@section('page-title', 'Data Kategori Barang')

@section('content')

    <x-ui.page-header title="Data Kategori Barang" subtitle="Kelola kategori untuk data barang">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.categories.create') }}" icon="plus">Tambah Kategori</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($categories->isEmpty())
            <x-ui.empty-state icon="tags" title="Belum ada kategori"
                description="Tambahkan kategori untuk mengelompokkan barang.">
                <x-slot:actions>
                    <x-ui.button href="{{ route('waka.categories.create') }}" icon="plus" size="sm">Tambah Kategori</x-ui.button>
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
                            <th class="px-4 py-3 font-medium">Nama Kategori</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="px-4 py-3 font-medium w-24">Jumlah Barang</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($categories as $i => $cat)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $cat->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $categories->firstItem() + $i }}</td>
                                <td class="px-4 py-3 font-medium text-surface-800">{{ $cat->name }}</td>
                                <td class="px-4 py-3 text-surface-600 max-w-md truncate">{{ $cat->description ?: '—' }}</td>
                                <td class="px-4 py-3"><x-ui.badge color="purple">{{ $cat->items_count }} barang</x-ui.badge></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('waka.categories.edit', $cat)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $cat->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($categories->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $categories->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Kategori"
        message="Apakah Anda yakin ingin menghapus kategori ini?" confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('waka.categories.bulk-action')"
    entity="kategori"
    :actions="['delete' => 'Hapus']"
    :confirm="true" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/waka/categories/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush