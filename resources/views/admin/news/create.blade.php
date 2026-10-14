@extends('layouts.backend')

@section('title', 'Tambah Berita')
@section('page-title', 'Data Berita')

@section('content')

    <x-ui.page-header title="Tambah Berita" subtitle="Buat berita atau artikel baru" />

    <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1 space-y-5">
                <x-ui.card title="Gambar Berita">
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

                <x-ui.card title="Publikasi">
                    <div class="space-y-4">
                        <x-ui.select name="status" label="Status" :required="true"
                            :options="['draft' => 'Draft', 'published' => 'Publikasikan']"
                            :value="old('status', 'draft')" :placeholder="null" />
                        <x-ui.input
                            name="published_at"
                            label="Tanggal & Waktu Publikasi"
                            type="datetime-local"
                            :value="old('published_at', now()->format('Y-m-d\TH:i'))"
                            :required="true"
                        />
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card title="Konten Berita">
                    <div class="space-y-4">
                        <x-ui.input name="title" label="Judul Berita" :value="old('title')" :required="true" icon="newspaper"
                                    placeholder="Judul berita..." />
                        <x-ui.textarea name="content" label="Isi Berita" rows="18" :required="true"
                            placeholder="Tulis isi berita...">{{ old('content') }}</x-ui.textarea>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.news.index') }}" variant="secondary">Batal</x-ui.button>
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