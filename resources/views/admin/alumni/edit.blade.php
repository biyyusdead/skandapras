@extends('layouts.backend')

@section('title', 'Edit Alumni')

@section('page-title', 'Data Alumni')

@section('content')

<x-ui.page-header title="Edit Alumni" subtitle="Perbarui data lulusan terbaik / alumni" />

<form action="{{ route('admin.alumni.update', $alumni) }}" method="POST" enctype="multipart/form-data" class="space-y-5">

    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ==================== FOTO ==================== --}}

        <div class="lg:col-span-1">

            <x-ui.card title="Foto Alumni">

                <div class="flex flex-col items-center">

                    <div class="w-full aspect-square rounded-2xl border-2 border-dashed border-surface-300
                                flex items-center justify-center overflow-hidden bg-surface-50 mb-4">

                        @if ($alumni->photo)

                            <img id="photo-preview"
                                 src="{{ asset('storage/' . $alumni->photo) }}"
                                 class="w-full h-full object-cover">

                            <div id="photo-placeholder" class="hidden"></div>

                        @else

                            <img id="photo-preview"
                                 src=""
                                 class="w-full h-full object-cover hidden">

                            <div id="photo-placeholder" class="text-center">

                                <i data-lucide="user" class="w-10 h-10 text-surface-400 mx-auto"></i>

                                <p class="text-xs text-surface-400 mt-2">Belum ada foto</p>

                            </div>

                        @endif

                    </div>

                    <label for="photo" class="cursor-pointer">

                        <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                     bg-surface-100 hover:bg-surface-200 text-surface-700 transition">

                            <i data-lucide="upload" class="w-4 h-4"></i>
                            {{ $alumni->photo ? 'Ganti Foto' : 'Pilih Foto' }}

                        </span>

                        <input
                            type="file"
                            id="photo"
                            name="photo"
                            accept="image/*"
                            class="hidden"
                            onchange="previewPhoto(event)"
                        >

                    </label>

                    <p class="text-[11px] text-surface-400 mt-2 text-center">
                        Biarkan kosong jika tidak ingin mengganti foto.
                    </p>

                    @error('photo')
                        <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                    @enderror

                </div>

            </x-ui.card>

        </div>

        {{-- ==================== DATA ==================== --}}

        <div class="lg:col-span-2">

            <x-ui.card title="Data Alumni">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div class="md:col-span-2">

                        <x-ui.input
                            name="name"
                            label="Nama Lengkap"
                            :value="old('name', $alumni->name)"
                            :required="true"
                            icon="user"
                        />

                    </div>

                    <x-ui.input
                        name="graduation_year"
                        label="Tahun Lulus"
                        type="number"
                        :value="old('graduation_year', $alumni->graduation_year)"
                        icon="calendar"
                        min="1990"
                        max="{{ date('Y') + 1 }}"
                    />

                    {{-- Jurusan — Dropdown --}}

                    <div>

                        <label for="department" class="block text-sm font-medium text-surface-700 mb-1.5">
                            Jurusan Saat Sekolah
                        </label>

                        <div class="relative">

                            <i data-lucide="graduation-cap"
                               class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>

                            <select
                                name="department"
                                id="department"
                                class="w-full pl-9 pr-9 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                       appearance-none cursor-pointer
                                       transition-all duration-150 hover:border-surface-400
                                       focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                                       @error('department') border-red-400 @enderror"
                            >

                                <option value="">— Pilih Jurusan —</option>

                                @foreach ($departments as $dept)

                                    <option
                                        value="{{ $dept->name }}"
                                        @selected(old('department', $alumni->department) === $dept->name)
                                    >
                                        {{ $dept->code }} — {{ $dept->name }}
                                    </option>

                                @endforeach

                            </select>

                            <i data-lucide="chevron-down"
                               class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>

                        </div>

                        @error('department')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        {{-- Info kalau data lama tidak ada di daftar --}}

                        @php
                            $inList = $departments->contains(
                                fn ($d) => $d->name === $alumni->department
                            );
                        @endphp

                        @if ($alumni->department && ! $inList)

                            <p class="mt-1.5 text-[11px] text-amber-600">
                                Nilai lama "<strong>{{ $alumni->department }}</strong>" tidak ada di daftar jurusan.
                                Pilih ulang dari dropdown untuk memperbarui.
                            </p>

                        @endif

                    </div>

                    <div class="md:col-span-2">

                        <x-ui.input
                            name="achievement"
                            label="Prestasi"
                            :value="old('achievement', $alumni->achievement)"
                            icon="trophy"
                        />

                    </div>

                    <div class="md:col-span-2">

                        <x-ui.input
                            name="motto"
                            label="Motto"
                            :value="old('motto', $alumni->motto)"
                            icon="quote"
                        />

                    </div>

                    <div class="md:col-span-2">

                        <x-ui.textarea
                            name="description"
                            label="Deskripsi"
                            rows="5"
                        >{{ old('description', $alumni->description) }}</x-ui.textarea>

                    </div>

                </div>

            </x-ui.card>

        </div>

    </div>

    <div class="flex items-center justify-end gap-2">

        <x-ui.button href="{{ route('admin.alumni.index') }}" variant="secondary">
            Batal
        </x-ui.button>

        <x-ui.button type="submit" icon="save" :loading="true">
            Simpan Perubahan
        </x-ui.button>

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

            if (placeholder) {
                placeholder.classList.add('hidden');
            }

        };

        reader.readAsDataURL(file);
    }
</script>

@endpush
