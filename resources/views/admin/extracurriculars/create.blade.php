@extends('layouts.backend')

@section('title', 'Tambah Ekstrakurikuler')
@section('page-title', 'Data Ekstrakurikuler')

@section('content')

    <x-ui.page-header title="Tambah Ekstrakurikuler" subtitle="Tambahkan kegiatan ekstrakurikuler baru" />

    <form action="{{ route('admin.extracurriculars.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1">
                <x-ui.card title="Gambar Ekskul">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center
                                    overflow-hidden bg-surface-50 mb-4">
                            <img id="image-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="image-placeholder" class="text-center">
                                <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                            </div>
                        </div>
                        <label for="image" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Pilih Gambar
                            </span>
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                        </label>
                        @error('image') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card title="Informasi Ekstrakurikuler">
                    <div class="space-y-4">
                        <x-ui.input name="name" label="Nama Ekstrakurikuler" :value="old('name')" :required="true" icon="activity" />
                        <x-ui.textarea name="description" label="Deskripsi" rows="5"
                            placeholder="Deskripsi kegiatan...">{{ old('description') }}</x-ui.textarea>
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
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.extracurriculars.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
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
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush