@extends('layouts.backend')

@section('title', 'Edit Produk')
@section('page-title', 'Produk Unggulan')

@section('content')

    <x-ui.page-header title="Edit Produk Unggulan" subtitle="Perbarui data produk" />

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1">
                <x-ui.card title="Gambar Produk">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-square rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center
                                    overflow-hidden bg-surface-50 mb-4">
                            @if ($product->image)
                                <img id="image-preview" src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
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
                <x-ui.card title="Informasi Produk">
                    <div class="space-y-4">
                        <x-ui.input name="name" label="Nama Produk" :value="old('name', $product->name)" :required="true" icon="package" />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <x-ui.input name="category" label="Kategori" :value="old('category', $product->category)"
                                        placeholder="Contoh: Teknologi, Kriya, Kuliner" icon="tag" />

                            <x-ui.input name="code" label="Kode Produk" :value="old('code', $product->code)"
                                        placeholder="Contoh: PRD-001" icon="hash" />
                        </div>

                        <x-ui.input name="contact" label="Kontak (WhatsApp)" :value="old('contact', $product->contact)"
                                    placeholder="Contoh: 08123456789" icon="phone" />

                        <x-ui.textarea name="description" label="Deskripsi" rows="6"
                            placeholder="Deskripsi produk...">{{ old('description', $product->description) }}</x-ui.textarea>

                        <div>
                            <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                            <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                        hover:border-primary-400 transition-colors duration-150">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))
                                    class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                                <span class="text-sm text-surface-700">Aktif</span>
                            </label>
                        </div>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.products.index') }}" variant="secondary">Batal</x-ui.button>
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