@extends('layouts.backend')

@section('title', 'Tambah Barang')
@section('page-title', 'Data Barang')

@section('content')

    <x-ui.page-header title="Tambah Barang"
        subtitle="Tambah jenis barang dan distribusikan ke ruangan" />

    <form action="{{ route('waka.items.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-5" x-data="itemForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <div class="lg:col-span-1">
                <x-ui.card title="Foto Barang">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-square rounded-xl border-2 border-dashed border-surface-300
                                    flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="photo-placeholder" class="text-center">
                                <i data-lucide="package" class="w-8 h-8 text-surface-400 mx-auto"></i>
                            </div>
                        </div>
                        <label for="photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Pilih Foto
                            </span>
                            <input type="file" id="photo" name="photo" accept="image/*"
                                   class="hidden" onchange="previewPhoto(event)">
                        </label>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2 space-y-5">

                <x-ui.card title="Informasi Barang">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Barang" :value="old('name')" :required="true" icon="package"
                                        placeholder="Contoh: Papan Tulis Putih" />
                        </div>
                        <x-ui.input name="code" label="Kode Barang" :value="old('code')" :required="true" icon="hash"
                                    placeholder="Contoh: PT-120" />
                        <x-ui.input name="brand" label="Merek" :value="old('brand')" icon="tag" />
                        <x-ui.select name="category_id" label="Kategori" :required="true"
                            :options="$categories->pluck('name', 'id')->toArray()"
                            :value="old('category_id')" />
                        <x-ui.input name="description" label="Deskripsi" :value="old('description')" icon="file-text" />
                    </div>
                </x-ui.card>

                <x-ui.card title="Distribusi ke Ruangan"
                    subtitle="Tentukan ruangan, jumlah, dan kondisi barang">
                    <div class="space-y-3">

                        {{-- Error umum --}}
                        @error('rooms')
                            <div class="p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
                                {{ $message }}
                            </div>
                        @enderror

                        <template x-for="(row, index) in rows" :key="index">
                            <div class="p-4 border border-surface-200 rounded-lg bg-surface-50/50 space-y-3">
                                <div class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-medium text-surface-500 uppercase tracking-wide">
                                        Ruangan #<span x-text="index + 1"></span>
                                    </span>
                                    <button type="button" x-on:click="removeRow(index)"
                                            x-show="rows.length > 1"
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

                        <button type="button" x-on:click="addRow()"
                                class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg
                                       border-2 border-dashed border-surface-300 hover:border-primary-400
                                       text-sm font-medium text-surface-500 hover:text-primary-600
                                       transition-all duration-150">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Tambah Ruangan
                        </button>
                    </div>
                </x-ui.card>

                <x-ui.card>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Barang Aktif</span>
                    </label>
                </x-ui.card>

            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.items.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    function itemForm() {
        return {
            rows: [{ room_id: '', quantity: 1, condition: 'baik', notes: '' }],
            addRow() {
                this.rows.push({ room_id: '', quantity: 1, condition: 'baik', notes: '' });
                this.$nextTick(() => lucide.createIcons());
            },
            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
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
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush