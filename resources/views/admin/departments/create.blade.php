@extends('layouts.backend')

@section('title', 'Tambah Jurusan')
@section('page-title', 'Data Jurusan')

@section('content')

    <x-ui.page-header title="Tambah Jurusan" subtitle="Buat program keahlian baru" />

    <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
        @csrf

        <x-ui.card title="Informasi Jurusan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="code" label="Kode Jurusan" :value="old('code')" :required="true"
                            placeholder="Contoh: RPL, TKJ" icon="hash" />
                <x-ui.input name="name" label="Nama Jurusan" :value="old('name')" :required="true"
                            placeholder="Contoh: Rekayasa Perangkat Lunak" icon="layers" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="description" label="Deskripsi" rows="4"
                        placeholder="Deskripsi singkat jurusan...">{{ old('description') }}</x-ui.textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">
                        Foto/Ilustrasi Jurusan
                    </label>

                    @if (isset($department) && $department->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $department->image) }}"
                                class="w-32 h-32 object-contain rounded-lg border border-surface-200 bg-surface-50 p-2">
                        </div>
                    @endif

                    <input type="file" name="image" accept="image/*"
                        class="w-full text-sm file:mr-3 file:py-2 file:px-4 file:rounded-lg
                                file:border-0 file:bg-primary-50 file:text-primary-700
                                hover:file:bg-primary-100 file:cursor-pointer">
                    <p class="text-xs text-surface-400 mt-1.5">
                        Format PNG/JPG/SVG. Maks 2MB. Rekomendasi ukuran: 400×400px dengan background transparan.
                    </p>
                    @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Aktif</span>
                    </label>
                </div>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.departments.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

@endsection