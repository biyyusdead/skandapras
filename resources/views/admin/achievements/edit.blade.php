@extends('layouts.backend')

@section('title', 'Edit Prestasi')
@section('page-title', 'Data Prestasi')

@section('content')

    <x-ui.page-header title="Edit Prestasi" subtitle="Perbarui data prestasi" />

    <form action="{{ route('admin.achievements.update', $achievement) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1">
                <x-ui.card title="Gambar Prestasi">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center
                                    overflow-hidden bg-surface-50 mb-4">
                            @if ($achievement->image)
                                <img id="image-preview" src="{{ asset('storage/' . $achievement->image) }}" class="w-full h-full object-cover">
                                <div id="image-placeholder" class="hidden"></div>
                            @else
                                <img id="image-preview" src="" class="w-full h-full object-cover hidden">
                                <div id="image-placeholder" class="text-center">
                                    <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                </div>
                            @endif
                        </div>
                        <label for="image" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Ganti Gambar
                            </span>
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                        </label>
                        @error('image') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card title="Detail Prestasi">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="title" label="Judul Prestasi" :value="old('title', $achievement->title)" :required="true" icon="trophy" />
                        </div>
                        <x-ui.input name="category" label="Kategori" :value="old('category', $achievement->category)" icon="tag" />
                        <x-ui.input name="achievement_date" label="Tanggal" type="date"
                                    :value="old('achievement_date', $achievement->achievement_date?->format('Y-m-d'))" icon="calendar" />
                        <div class="md:col-span-2">
                            <x-ui.textarea name="description" label="Deskripsi" rows="4">{{ old('description', $achievement->description) }}</x-ui.textarea>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.achievements.index') }}" variant="secondary">Batal</x-ui.button>
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