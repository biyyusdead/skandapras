@extends('layouts.backend')

@section('title', 'Edit Guru')
@section('page-title', 'Data Guru')

@section('content')

    <x-ui.page-header title="Edit Guru" subtitle="Perbarui data guru atau staf" />

    <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-1">
                <x-ui.card title="Foto Guru">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full border-2 border-dashed border-surface-300 flex items-center justify-center
                                    overflow-hidden bg-surface-50 mb-4">
                            @if ($teacher->photo)
                                <img id="photo-preview" src="{{ asset('storage/' . $teacher->photo) }}" class="w-full h-full object-cover">
                                <div id="photo-placeholder" class="hidden"></div>
                            @else
                                <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                                <div id="photo-placeholder" class="text-center">
                                    <i data-lucide="user" class="w-8 h-8 text-surface-400 mx-auto"></i>
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
                        <p class="text-xs text-surface-400 mt-2 text-center">Kosongkan jika tidak ingin mengubah.</p>
                        @error('photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            <div class="lg:col-span-2">
                <x-ui.card title="Informasi Guru">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="name" label="Nama Lengkap" :value="old('name', $teacher->name)" :required="true" icon="user" />
                        </div>
                        <x-ui.input name="nip" label="NIP" :value="old('nip', $teacher->nip)" icon="hash" />
                        <x-ui.input name="position" label="Jabatan" :value="old('position', $teacher->position)" icon="briefcase" />
                        <x-ui.input name="email" label="Email" type="email" :value="old('email', $teacher->email)" icon="mail" />
                        <x-ui.input name="phone" label="No. Telepon" :value="old('phone', $teacher->phone)" icon="phone" />
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                            <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                          hover:border-primary-400 transition-colors duration-150">
                                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $teacher->is_active))
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
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
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
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush