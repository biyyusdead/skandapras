@extends('layouts.backend')

@section('title', 'Data Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    <x-ui.page-header title="Data Ruangan" subtitle="Kelola data ruangan sekolah">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.rooms.create') }}" icon="plus">Tambah Ruangan</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filter --}}
    <x-ui.card :padding="false" class="mb-5">
        <div class="p-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kode, atau lokasi..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="condition" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                               transition-all duration-150 hover:border-surface-400
                                               focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                    <option value="">Semua Kondisi</option>
                    @foreach ($conditions as $c)
                        <option value="{{ $c->value }}" @selected(request('condition') === $c->value)>{{ $c->label() }}</option>
                    @endforeach
                </select>
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
    </x-ui.card>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-medium text-surface-600">
            Klik card untuk pilih multiple
        </h2>
        <label class="inline-flex items-center gap-2 text-sm text-surface-600 cursor-pointer">
            <input type="checkbox"
                data-bulk-select-all
                onchange="toggleAll(this)"
                class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
            Pilih Semua
        </label>
    </div>

    @if ($rooms->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="door-open" title="Belum ada ruangan"
                description="Tambahkan ruangan pertama untuk memulai.">
                <x-slot:actions>
                    <x-ui.button href="{{ route('waka.rooms.create') }}" icon="plus" size="sm">Tambah Ruangan</x-ui.button>
                </x-slot:actions>
            </x-ui.empty-state>
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($rooms as $room)
                <div class="group bg-white border border-surface-200/70 rounded-card shadow-card overflow-hidden hover-lift">
                    {{-- Checkbox — absolute di pojok --}}
                    <div class="absolute top-3 left-3 z-10">
                        <input type="checkbox"
                            data-bulk-checkbox
                            value="{{ $room->id }}"
                            onchange="toggleOne(this)"
                            class="w-5 h-5 rounded border-2 border-white bg-white/90
                                    text-primary-600 focus:ring-primary-500/30 cursor-pointer shadow-md">
                    </div>
                    {{-- Foto --}}
                    <div class="relative aspect-video bg-surface-100 overflow-hidden">
                        @if ($room->photo)
                            <img src="{{ asset('storage/' . $room->photo) }}" alt="{{ $room->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i data-lucide="door-open" class="w-10 h-10 text-surface-300"></i>
                            </div>
                        @endif

                        {{-- Panorama + 3D badge --}}
                        <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                            @if ($room->panorama_url || $room->panorama_file)
                                <div class="px-2 py-1 rounded-full bg-primary-600/90 text-white text-[10px] font-medium
                                            backdrop-blur-sm flex items-center gap-1">
                                    <i data-lucide="rotate-3d" class="w-3 h-3"></i> 360°
                                </div>
                            @endif

                            @if ($room->model_3d_x !== null && $room->model_3d_y !== null && $room->model_3d_visible)
                                <div class="px-2 py-1 rounded-full bg-emerald-600/90 text-white text-[10px] font-medium
                                            backdrop-blur-sm flex items-center gap-1"
                                    title="Muncul di Model 3D">
                                    <i data-lucide="box" class="w-3 h-3"></i> 3D
                                </div>
                            @endif
                        </div>

                        {{-- Status badge --}}
                        <div class="absolute top-3 left-3">
                            @if ($room->is_active)
                                <x-ui.badge color="green" :dot="true">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge color="red" :dot="true">Tidak Aktif</x-ui.badge>
                            @endif
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-surface-900 truncate">{{ $room->name }}</h3>
                                <p class="text-xs text-surface-500 mt-0.5">
                                    <span class="font-mono">{{ $room->code }}</span>
                                    @if ($room->floor) · Lantai {{ $room->floor }} @endif
                                </p>
                            </div>
                        </div>

                        {{-- Badges --}}
                        <div class="flex items-center gap-1.5 flex-wrap mb-3">
                            <x-ui.badge :color="$room->condition->color()">{{ $room->condition->label() }}</x-ui.badge>
                            <x-ui.badge color="gray" icon="package">{{ $room->items_count }} barang</x-ui.badge>
                            <x-ui.badge color="gray" icon="users">{{ $room->room_users_count }} PJ</x-ui.badge>
                        </div>

                        {{-- Health score bar --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-medium text-surface-500 uppercase tracking-wide">Health Score</span>
                                <span class="text-xs font-bold text-surface-800 tabular-nums">{{ $room->health_score }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-surface-100 rounded-full overflow-hidden">
                                @php
                                    $scoreColor = $room->health_score >= 70 ? 'bg-green-500' : ($room->health_score >= 40 ? 'bg-yellow-500' : 'bg-red-500');
                                @endphp
                                <div class="h-full {{ $scoreColor }} rounded-full transition-all duration-500"
                                     style="width: {{ $room->health_score }}%"></div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-1 pt-3 border-t border-surface-100">
                            <x-ui.table-action :href="route('waka.rooms.show', $room)" icon="eye" title="Detail" />
                            <x-ui.table-action :href="route('waka.rooms.edit', $room)" icon="pencil" title="Edit" />
                            <div class="flex-1"></div>
                            <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                :onclick="'confirmDelete(' . $room->id . ')'" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($rooms->hasPages())
            <div class="mt-5">{{ $rooms->links() }}</div>
        @endif
    @endif

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Ruangan"
        message="Apakah Anda yakin ingin menghapus ruangan ini? Semua barang dan laporan terkait juga akan terhapus."
        confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('waka.rooms.bulk-action')"
    entity="ruangan"
    :actions="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
    :confirm="true" />
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/waka/rooms/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush