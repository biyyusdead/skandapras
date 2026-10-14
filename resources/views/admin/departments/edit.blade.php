@extends('layouts.backend')

@section('title', 'Edit Jurusan')
@section('page-title', 'Data Jurusan')

@section('content')

    <x-ui.page-header title="Edit Jurusan" subtitle="Perbarui data jurusan" />

    <form action="{{ route('admin.departments.update', $department) }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-5">
        @csrf @method('PUT')

        <x-ui.card title="Informasi Jurusan">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                {{-- KOLOM KIRI: Upload Gambar --}}
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-surface-700 mb-2">
                        Foto/Ilustrasi Jurusan
                    </label>

                    {{-- Preview --}}
                    <div class="w-full aspect-square rounded-xl border-2 border-dashed border-surface-300
                                flex items-center justify-center overflow-hidden bg-surface-50 mb-3">
                        @if ($department->image)
                            <img id="image-preview"
                                 src="{{ asset('storage/' . $department->image) }}"
                                 class="w-full h-full object-contain p-3">
                            <div id="image-placeholder" class="hidden"></div>
                        @else
                            <img id="image-preview" src="" class="w-full h-full object-contain p-3 hidden">
                            <div id="image-placeholder" class="text-center">
                                <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                <p class="text-xs text-surface-400 mt-1">Belum ada gambar</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Pilih --}}
                    <label for="image" class="cursor-pointer block">
                        <span class="inline-flex items-center justify-center gap-2 w-full px-3.5 py-2 text-sm font-medium
                                     rounded-lg bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            {{ $department->image ? 'Ganti Gambar' : 'Pilih Gambar' }}
                        </span>
                        <input type="file" id="image" name="image" accept="image/*"
                               class="hidden" onchange="previewImage(event)">
                    </label>

                    <p class="text-xs text-surface-400 mt-2">
                        Format PNG/JPG/SVG. Maks 2MB. Rekomendasi 400×400px.
                    </p>

                    @error('image')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KOLOM KANAN: Field Data --}}
                <div class="md:col-span-2 space-y-4">
                    <x-ui.input name="code" label="Kode Jurusan"
                                :value="old('code', $department->code)" :required="true" icon="hash" />

                    <x-ui.input name="name" label="Nama Jurusan"
                                :value="old('name', $department->name)" :required="true" icon="layers" />

                    <x-ui.textarea name="description" label="Deskripsi" rows="5">{{ old('description', $department->description) }}</x-ui.textarea>

                    <div>
                        <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                        <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                      hover:border-primary-400 transition-colors duration-150">
                            <input type="checkbox" name="is_active" value="1"
                                   @checked(old('is_active', $department->is_active))
                                   class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                            <span class="text-sm text-surface-700">Aktif</span>
                        </label>
                    </div>
                </div>

            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.departments.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush