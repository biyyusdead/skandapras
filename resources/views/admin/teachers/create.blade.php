@extends('layouts.backend')

@section('title', 'Tambah Guru')
@section('page-title', 'Data Guru')

@section('content')

    <x-ui.page-header title="Tambah Guru" subtitle="Tambahkan data guru atau staf baru" />

    <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            {{-- Kolom Kiri --}}
            <div class="lg:col-span-1">
                <x-ui.card title="Foto Guru">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full border-2 border-dashed border-surface-300 flex items-center justify-center
                                    overflow-hidden bg-surface-50 mb-4">
                            <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="photo-placeholder" class="text-center">
                                <i data-lucide="user" class="w-8 h-8 text-surface-400 mx-auto"></i>
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
                        <p class="text-xs text-surface-400 mt-2 text-center">PNG, JPG, WebP. Maks 2MB.</p>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- Kolom Kanan --}}
            <div class="lg:col-span-2">
                <x-ui.card title="Informasi Guru">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Lengkap" :value="old('name')" :required="true" icon="user" />
                        </div>
                        <x-ui.input name="nip" label="NIP" :value="old('nip')" icon="hash" placeholder="18 digit" />
                        <x-ui.input name="position" label="Jabatan" :value="old('position')" icon="briefcase"
                                    placeholder="Contoh: Guru RPL" />
                        <x-ui.input name="email" label="Email" type="email" :value="old('email')" icon="mail" />
                        <x-ui.input name="phone" label="No. Telepon" :value="old('phone')" icon="phone" placeholder="08xx" />
                        <div class="md:col-span-2">
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
            <x-ui.button href="{{ route('admin.teachers.index') }}" variant="secondary">Batal</x-ui.button>
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
</script>
@endpush