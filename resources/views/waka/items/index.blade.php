@extends('layouts.backend')

@section('title', 'Data Barang')
@section('page-title', 'Data Barang')

@section('content')

    <x-ui.page-header title="Data Barang" subtitle="Kelola jenis barang dan distribusinya">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.items.create') }}" icon="plus">Tambah Barang</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        {{-- Filter --}}
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2">
                <div class="relative lg:col-span-2">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama, kode, atau merek..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="category_id" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="room_id" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Ruangan</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(request('room_id') == $room->id)>{{ $room->name }}</option>
                    @endforeach
                </select>
                <select name="condition" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Kondisi</option>
                    @foreach ($conditions as $c)
                        <option value="{{ $c->value }}" @selected(request('condition') === $c->value)>{{ $c->label() }}</option>
                    @endforeach
                </select>
                <div class="lg:col-span-5 flex gap-2">
                    <x-ui.button type="submit" variant="secondary" icon="filter" size="sm">Filter</x-ui.button>
                    @if (request()->hasAny(['search', 'category_id', 'room_id', 'condition']))
                        <x-ui.button href="{{ route('waka.items.index') }}" variant="ghost" icon="x" size="sm">Reset</x-ui.button>
                    @endif
                </div>
            </form>
        </div>

        @if ($items->isEmpty())
            <x-ui.empty-state icon="package" title="Belum ada barang">
                <x-slot:actions>
                    <x-ui.button href="{{ route('waka.items.create') }}" icon="plus" size="sm">Tambah Barang</x-ui.button>
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
                            <th class="px-4 py-3 font-medium w-16">Foto</th>
                            <th class="px-4 py-3 font-medium">Nama Barang</th>
                            <th class="px-4 py-3 font-medium w-24">Kode</th>
                            <th class="px-4 py-3 font-medium">Kategori</th>
                            <th class="px-4 py-3 font-medium w-24">Total Unit</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($items as $i => $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $item->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $items->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->photo)
                                        <img src="{{ asset('storage/' . $item->photo) }}"
                                             class="w-10 h-10 rounded-lg object-cover ring-1 ring-surface-200">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-surface-100 flex items-center justify-center">
                                            <i data-lucide="package" class="w-4 h-4 text-surface-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-800">{{ $item->name }}</p>
                                    @if ($item->brand) <p class="text-xs text-surface-400">{{ $item->brand }}</p> @endif
                                </td>
                                <td class="px-4 py-3"><span class="text-xs font-mono text-surface-600">{{ $item->code }}</span></td>
                                <td class="px-4 py-3"><x-ui.badge color="purple">{{ $item->category->name }}</x-ui.badge></td>
                                <td class="px-4 py-3">
                                    <x-ui.badge color="gray" icon="package">{{ $item->total_quantity }} unit</x-ui.badge>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->itemRooms->isEmpty())
                                        <span class="text-xs text-surface-400 italic">Belum didistribusikan</span>
                                    @else
                                        <div class="flex items-center gap-1 flex-wrap max-w-xs">
                                            @foreach ($item->itemRooms->take(3) as $ir)
                                                <a href="{{ route('waka.rooms.show', $ir->room) }}"
                                                   class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
                                                          bg-surface-100 hover:bg-primary-50 text-surface-700 hover:text-primary-700
                                                          text-xs transition-colors">
                                                    {{ $ir->room->name }}
                                                    <span class="font-semibold">·{{ $ir->quantity }}</span>
                                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $ir->condition->color() }}-500"></span>
                                                </a>
                                            @endforeach
                                            @if ($item->itemRooms->count() > 3)
                                                <span class="text-xs text-surface-400">+{{ $item->itemRooms->count() - 3 }} lainnya</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('waka.items.edit', $item)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $item->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $items->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Barang"
        message="Apakah Anda yakin ingin menghapus barang ini? Distribusi ke semua ruangan juga akan terhapus."
        confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('waka.items.bulk-action')"
    entity="barang"
    :actions="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
    :confirm="true" />

@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/waka/items/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush