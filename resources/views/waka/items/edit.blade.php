@extends('layouts.backend')

@section('title', 'Edit Barang')
@section('page-title', 'Data Barang')

@section('content')

    <x-ui.page-header title="Edit Barang"
        subtitle="Perbarui data barang dan distribusinya" />

    <form action="{{ route('waka.items.update', $item) }}" method="POST" enctype="multipart/form-data"
          class="space-y-5" x-data="itemForm()">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- KIRI: Foto --}}
            <div class="lg:col-span-1">
                <x-ui.card title="Foto Barang">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-square rounded-xl border-2 border-dashed border-surface-300
                                    flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            @if ($item->photo)
                                <img id="photo-preview" src="{{ asset('storage/' . $item->photo) }}"
                                     class="w-full h-full object-cover">
                                <div id="photo-placeholder" class="hidden"></div>
                            @else
                                <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                                <div id="photo-placeholder" class="text-center">
                                    <i data-lucide="package" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                </div>
                            @endif
                        </div>
                        <label for="photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Ganti Foto
                            </span>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                   class="hidden" onchange="previewPhoto(event)">
                        </label>
                        <p class="text-xs text-surface-400 mt-2 text-center">Kosongkan jika tidak ingin mengubah.</p>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- KANAN: Info + Distribusi --}}
            <div class="lg:col-span-2 space-y-5">

                <x-ui.card title="Informasi Barang">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Barang"
                                        :value="old('name', $item->name)" :required="true" icon="package"
                                        placeholder="Contoh: Papan Tulis Putih" />
                        </div>
                        <x-ui.input name="code" label="Kode Barang"
                                    :value="old('code', $item->code)" :required="true" icon="hash"
                                    placeholder="Contoh: PT-120" />
                        <x-ui.input name="brand" label="Merek"
                                    :value="old('brand', $item->brand)" icon="tag" />
                        <x-ui.select name="category_id" label="Kategori" :required="true"
                            :options="$categories->pluck('name', 'id')->toArray()"
                            :value="old('category_id', $item->category_id)" />
                        <x-ui.input name="description" label="Deskripsi"
                                    :value="old('description', $item->description)" icon="file-text" />
                    </div>
                </x-ui.card>

                {{-- Distribusi --}}
                <x-ui.card title="Distribusi ke Ruangan" subtitle="Tentukan ruangan, jumlah, dan kondisi barang">

                    {{-- Data awal untuk Alpine --}}
                    <div
                        x-data="itemDistribution({
                            initialRows: {{ Js::from(
                                $item->itemRooms->map(fn ($ir) => [
                                    'room_id'   => $ir->room_id,
                                    'quantity'  => $ir->quantity,
                                    'condition' => $ir->condition?->value ?? 'baik',
                                    'notes'     => $ir->notes ?? '',
                                ])->values()
                            ) }},
                            rooms: {{ Js::from($rooms->map(fn ($r) => ['id' => $r->id, 'name' => $r->name, 'code' => $r->code])->values()) }},
                            perPage: 5
                        })"
                        class="space-y-3"
                    >

                        @error('rooms')
                            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                                {{ $message }}
                            </div>
                        @enderror

                        {{-- ===== SEARCHBAR ===== --}}
                        <div class="rounded-lg border border-surface-200 bg-surface-50/50 p-3 space-y-2">
                            <label class="block text-xs font-medium text-surface-600">Cari Ruangan</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-surface-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" x-model.debounce.200ms="search"
                                    placeholder="Cari nama / kode ruangan..."
                                    class="w-full pl-9 pr-9 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                            focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                <button type="button" x-show="search.length > 0" x-on:click="search = ''"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-surface-400 hover:text-surface-600">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>
                            <p class="text-xs text-surface-400">
                                Menampilkan
                                <span class="font-semibold text-surface-600" x-text="total"></span>
                                dari
                                <span class="font-semibold text-surface-600" x-text="rows.length"></span>
                                ruangan
                                <span x-show="search.length > 0">(hasil pencarian)</span>
                            </p>
                        </div>

                        {{-- ===== ROWS (semua ada di DOM, hanya di-hide) ===== --}}
                        <template x-for="(row, index) in rows" :key="row._key">
                            <div
                                x-show="isVisible(index)"
                                x-transition.opacity.duration.150ms
                                class="p-4 border border-surface-200 rounded-lg bg-surface-50/50 space-y-3"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">
                                        Ruangan #<span x-text="index + 1"></span>
                                    </span>
                                    <button type="button" x-on:click="removeRow(index)"
                                            class="p-1 rounded-md text-surface-400 hover:text-red-600 hover:bg-red-50 transition">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-surface-600 mb-1">Ruangan *</label>
                                        <select :name="`rooms[${index}][room_id]`" x-model="row.room_id" required
                                                class="w-full px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                                    focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                            <option value="">— Pilih Ruangan —</option>
                                            @foreach ($rooms as $room)
                                                <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-surface-600 mb-1">Jumlah *</label>
                                        <input type="number" :name="`rooms[${index}][quantity]`" x-model="row.quantity"
                                            min="1" required
                                            class="w-full px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                                    focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-surface-600 mb-1">Kondisi *</label>
                                        <select :name="`rooms[${index}][condition]`" x-model="row.condition" required
                                                class="w-full px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                                    focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                            @foreach ($conditions as $c)
                                                <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-medium text-surface-600 mb-1">Catatan (opsional)</label>
                                        <input type="text" :name="`rooms[${index}][notes]`" x-model="row.notes"
                                            placeholder="Keterangan tambahan..."
                                            class="w-full px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                                    focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Empty state kalau hasil search kosong --}}
                        <div x-show="total === 0"
                            class="rounded-xl border border-dashed border-surface-300 bg-white p-8 text-center">
                            <p class="text-sm text-surface-400">Tidak ada ruangan yang cocok dengan pencarian.</p>
                        </div>

                        {{-- ===== PAGINATION (5 card per halaman) ===== --}}
                        <nav x-show="totalPages > 1"
                            class="flex items-center justify-between gap-3 border-t border-surface-200 pt-4">

                            <p class="text-xs text-surface-500">
                                Halaman
                                <span class="font-semibold text-surface-700" x-text="page"></span>
                                dari
                                <span class="font-semibold text-surface-700" x-text="totalPages"></span>
                            </p>

                            <div class="flex items-center gap-1">
                                {{-- Prev --}}
                                <button type="button" x-on:click="goPage(page - 1)" :disabled="page === 1"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-surface-200 text-surface-600 transition hover:border-primary-400 hover:text-primary-600 disabled:cursor-not-allowed disabled:text-surface-300 disabled:hover:border-surface-200 disabled:hover:text-surface-300">
                                    ‹
                                </button>

                                {{-- Page numbers --}}
                                <template x-for="p in pageNumbers()" :key="p">
                                    <button type="button" x-on:click="goPage(p)"
                                            :class="p === page
                                                ? 'bg-primary-600 border-primary-500 text-white'
                                                : 'border-surface-200 text-surface-600 hover:border-primary-400 hover:text-primary-600'"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border text-xs font-medium transition"
                                            x-text="p">
                                    </button>
                                </template>

                                {{-- Next --}}
                                <button type="button" x-on:click="goPage(page + 1)" :disabled="page === totalPages"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-surface-200 text-surface-600 transition hover:border-primary-400 hover:text-primary-600 disabled:cursor-not-allowed disabled:text-surface-300 disabled:hover:border-surface-200 disabled:hover:text-surface-300">
                                    ›
                                </button>
                            </div>
                        </nav>

                        {{-- ===== TOMBOL TAMBAH ===== --}}
                        <button type="button" x-on:click="addRow()"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg
                                    border-2 border-dashed border-surface-300 hover:border-primary-400
                                    text-sm font-medium text-surface-500 hover:text-primary-600
                                    transition-all duration-150">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Tambah Ruangan
                        </button>

                        <p class="text-xs text-surface-400">
                            Hapus ruangan dari daftar jika barang tidak lagi berada di sana.
                        </p>
                    </div>

                    @push('scripts')
                        <script>
                            function itemDistribution({ initialRows, rooms, perPage = 5 }) {
                                return {
                                    rows: initialRows.map((r, i) => ({
                                        ...r,
                                        _key: 'init-' + i + '-' + Date.now() + '-' + Math.random(),
                                    })),
                                    rooms,
                                    perPage,
                                    search: '',
                                    page: 1,

                                    /* ==== Filter ==== */
                                    get filteredIndexes() {
                                        const q = this.search.trim().toLowerCase();
                                        if (q === '') return this.rows.map((_, i) => i);

                                        return this.rows
                                            .map((r, i) => ({ r, i }))
                                            .filter(({ r }) => {
                                                const room = this.rooms.find(x => String(x.id) === String(r.room_id));
                                                if (!room) return false;
                                                const label = (room.name + ' ' + room.code).toLowerCase();
                                                return label.includes(q);
                                            })
                                            .map(({ i }) => i);
                                    },

                                    get total() {
                                        return this.filteredIndexes.length;
                                    },

                                    get totalPages() {
                                        return Math.max(1, Math.ceil(this.total / this.perPage));
                                    },

                                    get visibleIndexes() {
                                        const from = (this.page - 1) * this.perPage;
                                        return this.filteredIndexes.slice(from, from + this.perPage);
                                    },

                                    isVisible(index) {
                                        return this.visibleIndexes.includes(index);
                                    },

                                    pageNumbers() {
                                        const tp  = this.totalPages;
                                        const cur = this.page;
                                        const from = Math.max(1, cur - 2);
                                        const to   = Math.min(tp, cur + 2);
                                        const arr  = [];
                                        for (let i = from; i <= to; i++) arr.push(i);
                                        return arr;
                                    },

                                    goPage(p) {
                                        if (p < 1 || p > this.totalPages || p === this.page) return;
                                        this.page = p;
                                    },

                                    removeRow(index) {
                                        this.rows.splice(index, 1);
                                        if (this.page > this.totalPages) this.page = this.totalPages;
                                    },

                                    addRow() {
                                        this.rows.push({
                                            _key: 'new-' + Date.now() + '-' + Math.random(),
                                            room_id: '',
                                            quantity: 1,
                                            condition: 'baik',
                                            notes: '',
                                        });
                                        // Reset search & lompat ke halaman terakhir supaya row baru kelihatan
                                        this.search = '';
                                        this.$nextTick(() => { this.page = this.totalPages; });
                                    },

                                    /* Auto-reset page kalau search berubah */
                                    init() {
                                        this.$watch('search', () => { this.page = 1; });
                                    },
                                };
                            }
                        </script>
                    @endpush
                </x-ui.card>

                <x-ui.card>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1"
                               @checked(old('is_active', $item->is_active))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Barang Aktif</span>
                    </label>
                </x-ui.card>

            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.items.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    @php
        $initialRows = old('rooms', $item->itemRooms->map(function ($ir) {
            return [
                'room_id'   => $ir->room_id,
                'quantity'  => $ir->quantity,
                'condition' => $ir->condition->value,
                'notes'     => $ir->notes,
            ];
        })->values()->toArray());

        if (empty($initialRows)) {
            $initialRows = [
                ['room_id' => '', 'quantity' => 1, 'condition' => 'baik', 'notes' => '']
            ];
        }
    @endphp

    function itemForm() {
        return {
            rows: {!! json_encode($initialRows) !!},
            addRow() {
                this.rows.push({ room_id: '', quantity: 1, condition: 'baik', notes: '' });
                this.$nextTick(() => lucide.createIcons());
            },
            removeRow(index) {
                this.rows.splice(index, 1);
            }
        }
    }

    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush