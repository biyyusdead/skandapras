@extends('layouts.backend')

@section('title', 'Edit Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    <x-ui.page-header title="Edit Ruangan" subtitle="Perbarui data ruangan" />

    <form action="{{ route('waka.rooms.update', $room) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1 space-y-5">
                <x-ui.card title="Foto Ruangan">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300
                                    flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            @if ($room->photo)
                                <img id="photo-preview" src="{{ asset('storage/' . $room->photo) }}" class="w-full h-full object-cover">
                                <div id="photo-placeholder" class="hidden"></div>
                            @else
                                <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                                <div id="photo-placeholder" class="text-center">
                                    <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                </div>
                            @endif
                        </div>
                        <label for="photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Ganti Foto
                            </span>
                            <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                        </label>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>

                <x-ui.card title="Pengguna Ruangan">
                    @php $assignedIds = $room->roomUsers->pluck('user_id')->toArray(); @endphp
                    @if ($users->isEmpty())
                        <p class="text-sm text-surface-500">Belum ada pengguna ruangan.</p>
                    @else
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($users as $user)
                                <label class="flex items-center gap-2 px-3 py-2 border border-surface-200 rounded-lg cursor-pointer
                                              hover:border-primary-400 hover:bg-primary-50/30 transition">
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                           @checked(in_array($user->id, old('users', $assignedIds)))
                                           class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                                    <span class="text-sm text-surface-700">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>

            <div class="lg:col-span-2 space-y-5">
                <x-ui.card title="Informasi Ruangan">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Ruangan" :value="old('name', $room->name)" :required="true" icon="door-open" />
                        </div>
                        <x-ui.input name="code" label="Kode Ruangan" :value="old('code', $room->code)" :required="true" icon="hash" />
                        <x-ui.input name="floor" label="Lantai" :value="old('floor', $room->floor)" icon="layers" />
                        <x-ui.input name="capacity" label="Kapasitas" type="number" :value="old('capacity', $room->capacity)" icon="users" />
                        <div class="md:col-span-2">
                            <x-ui.textarea name="description" label="Deskripsi" rows="3">{{ old('description', $room->description) }}</x-ui.textarea>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="Kondisi & Virtual Tour">
                    <div class="space-y-5">

                        {{-- Kondisi --}}
                        <x-ui.select name="condition" label="Kondisi" :required="true"
                            :options="collect($conditions)->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray()"
                            :value="old('condition', $room->condition->value ?? 'baik')" :placeholder="null" />

                        {{-- Upload File Panorama --}}
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                Upload Foto 360° (Pannellum)
                            </label>

                            @if ($room->panorama_file)
                                <div class="mb-3 flex items-center gap-3 p-3 rounded-lg bg-primary-50 border border-primary-200">
                                    <i data-lucide="image" class="w-5 h-5 text-primary-600 shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-primary-700 truncate">
                                            {{ basename($room->panorama_file) }}
                                        </p>
                                        <p class="text-[10px] text-primary-500">File 360° aktif</p>
                                    </div>
                                    <a href="{{ asset('storage/' . $room->panorama_file) }}"
                                       target="_blank"
                                       class="text-xs text-primary-600 hover:text-primary-700 font-medium shrink-0">
                                        Lihat
                                    </a>
                                    <button type="button"
                                            onclick="confirmDeletePanorama()"
                                            class="text-xs text-red-600 hover:text-red-700 font-medium shrink-0">
                                        Hapus
                                    </button>
                                </div>
                            @endif

                            <input type="file"
                                name="panorama_file"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                        file:border-0 file:bg-primary-50 file:text-primary-700
                                        hover:file:bg-primary-100 file:cursor-pointer">
                            <p class="text-xs text-surface-400 mt-1.5">
                                File equirectangular (.jpg/.png), rasio 2:1, maks 20MB.
                                {{ $room->panorama_file ? 'Kosongkan jika tidak ingin ganti.' : '' }}
                            </p>
                            @error('panorama_file')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Divider --}}
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-surface-200"></div>
                            </div>
                            <div class="relative flex justify-center">
                                <span class="px-3 bg-white text-xs text-surface-400 uppercase tracking-wider">atau</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                URL Virtual Tour 360° (Kuula / Google Maps)
                            </label>
                            <input type="url"
                                name="panorama_url"
                                value="{{ old('panorama_url', $room->panorama_url) }}"
                                placeholder="https://kuula.co/share/xxxxx"
                                class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                                        focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @error('panorama_url')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Koordinat Peta 2D --}}
                        <div class="pt-4 border-t border-surface-100">
                            <p class="text-sm font-semibold text-surface-800 mb-3 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4 text-primary-600"></i>
                                Koordinat di Denah Sekolah (Peta 2D)
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                        Posisi X (%)
                                    </label>
                                    <input type="number"
                                        name="map_x"
                                        value="{{ old('map_x', $room->map_x) }}"
                                        min="0" max="100" step="0.01"
                                        placeholder="Contoh: 35.5"
                                        class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    <p class="text-xs text-surface-400 mt-1">0 = paling kiri, 100 = paling kanan</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                        Posisi Y (%)
                                    </label>
                                    <input type="number"
                                        name="map_y"
                                        value="{{ old('map_y', $room->map_y) }}"
                                        min="0" max="100" step="0.01"
                                        placeholder="Contoh: 42.0"
                                        class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    <p class="text-xs text-surface-400 mt-1">0 = paling atas, 100 = paling bawah</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                        Lantai
                                    </label>
                                    <input type="text"
                                        name="map_floor"
                                        value="{{ old('map_floor', $room->map_floor) }}"
                                        placeholder="1 / 2 / 3"
                                        class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    <p class="text-xs text-surface-400 mt-1">Untuk filter per lantai</p>
                                </div>
                            </div>
                        </div>

                        {{-- ⬇️ INCLUDE: Konfigurasi Model 3D --}}
                        @include('waka.rooms.partials.model-3d-fields', ['room' => $room])

                    </div>
                </x-ui.card>

                <x-ui.card>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $room->is_active))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Ruangan Aktif</span>
                    </label>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.rooms.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
        </div>
    </form>

    {{-- Form hapus panorama (hidden) --}}
    <form id="delete-panorama-form"
        method="POST"
        action="{{ route('waka.rooms.delete-panorama', $room) }}"
        style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <x-ui.confirm-modal
        id="delete-panorama"
        title="Hapus File Panorama"
        message="Apakah Anda yakin ingin menghapus file 360° ruangan ini? Tindakan ini tidak dapat dibatalkan."
        confirm-text="Ya, Hapus"
    />

@endsection

@push('scripts')
<script>
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

    function confirmDeletePanorama() {
        const form = document.getElementById('delete-panorama-form');
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: {
                id: 'delete-panorama',
                action: form.action,
                method: 'DELETE',
            }
        }));
    }

    // Sinkron preview warna hex untuk model 3D
    (function () {
        const colorPicker = document.querySelector('input[name="model_3d_color"]');
        const preview = document.getElementById('model-3d-color-preview');
        if (colorPicker && preview) {
            colorPicker.addEventListener('input', (e) => {
                preview.value = e.target.value;
            });
        }
    })();
</script>
@endpush