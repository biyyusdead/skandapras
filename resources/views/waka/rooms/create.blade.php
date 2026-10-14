@extends('layouts.backend')

@section('title', 'Tambah Ruangan')
@section('page-title', 'Data Ruangan')

@section('content')

    <x-ui.page-header title="Tambah Ruangan" subtitle="Tambahkan data ruangan baru" />

    <form action="{{ route('waka.rooms.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Kolom Kiri --}}
            <div class="lg:col-span-1 space-y-5">
                <x-ui.card title="Foto Ruangan">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300
                                    flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="photo-placeholder" class="text-center">
                                <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                <p class="text-xs text-surface-400 mt-1">Belum ada foto</p>
                            </div>
                        </div>
                        <label for="photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Pilih Foto
                            </span>
                            <input type="file" id="photo" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(event)">
                        </label>
                        <p class="text-xs text-surface-400 mt-2 text-center">JPG, PNG, WebP. Maks 3MB.</p>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>

                <x-ui.card title="Pengguna Ruangan">
                    @if ($users->isEmpty())
                        <p class="text-sm text-surface-500">Belum ada pengguna ruangan. Tambahkan dulu di Data Users.</p>
                    @else
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @foreach ($users as $user)
                                <label class="flex items-center gap-2 px-3 py-2 border border-surface-200 rounded-lg cursor-pointer
                                              hover:border-primary-400 hover:bg-primary-50/30 transition">
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}"
                                           @checked(in_array($user->id, old('users', [])))
                                           class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                                    <span class="text-sm text-surface-700">{{ $user->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                </x-ui.card>
            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-2 space-y-5">
                <x-ui.card title="Informasi Ruangan">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Ruangan" :value="old('name')" :required="true" icon="door-open"
                                        placeholder="Contoh: Lab RPL 1" />
                        </div>
                        <x-ui.input name="code" label="Kode Ruangan" :value="old('code')" :required="true" icon="hash"
                                    placeholder="Contoh: LAB-RPL-01" />
                        <x-ui.input name="floor" label="Lantai" :value="old('floor')" icon="layers"
                                    placeholder="Contoh: 1" />
                        <x-ui.input name="capacity" label="Kapasitas" type="number" :value="old('capacity')" icon="users"
                                    placeholder="Contoh: 36" />
                        <div class="md:col-span-2">
                            <x-ui.textarea name="description" label="Deskripsi" rows="3"
                                placeholder="Deskripsi ruangan...">{{ old('description') }}</x-ui.textarea>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="Kondisi & Virtual Tour">
                    <div class="space-y-5">

                        {{-- Kondisi --}}
                        <x-ui.select name="condition" label="Kondisi" :required="true"
                            :options="collect($conditions)->mapWithKeys(fn($c) => [$c->value => $c->label()])->toArray()"
                            :value="old('condition', 'baik')" :placeholder="null" />

                        {{-- Upload File Panorama --}}
                        <div>
                            <label for="panorama_file" class="cursor-pointer block">
                                <div class="w-full flex items-center justify-between gap-3 px-4 py-3
                                            border-2 border-dashed border-surface-300 hover:border-primary-400
                                            rounded-xl bg-surface-50 hover:bg-primary-50/30 transition-all">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 flex items-center justify-center shrink-0">
                                            <i data-lucide="upload" class="w-5 h-5 text-primary-600"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-surface-700" id="panorama-file-name">
                                                Pilih file 360° (.jpg/.png)
                                            </p>
                                            <p class="text-xs text-surface-400">Rasio 2:1, maks 20MB</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700
                                                text-white text-xs font-medium transition">
                                        Browse
                                    </span>
                                </div>
                            </label>
                            <input type="file"
                                id="panorama_file"
                                name="panorama_file"
                                accept=".jpg,.jpeg,.png,.webp"
                                class="hidden"
                                onchange="document.getElementById('panorama-file-name').textContent = this.files[0]?.name || 'Pilih file 360° (.jpg/.png)'">
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

                        {{-- URL Embed --}}
                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1.5">
                                URL Virtual Tour 360° (Kuula / Google Maps)
                            </label>
                            <input type="url"
                                name="panorama_url"
                                value="{{ old('panorama_url') }}"
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
                                    <input type="number" name="map_x"
                                        value="{{ old('map_x') }}"
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
                                    <input type="number" name="map_y"
                                        value="{{ old('map_y') }}"
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
                                    <input type="text" name="map_floor"
                                        value="{{ old('map_floor') }}"
                                        placeholder="1 / 2 / 3"
                                        class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg
                                                focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                    <p class="text-xs text-surface-400 mt-1">Untuk filter per lantai</p>
                                </div>
                            </div>
                        </div>

                        {{-- ⬇️ INCLUDE: Konfigurasi Model 3D --}}
                        @include('waka.rooms.partials.model-3d-fields')

                    </div>
                </x-ui.card>

                <x-ui.card>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Ruangan Aktif</span>
                    </label>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.rooms.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

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
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
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