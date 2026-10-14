@extends('layouts.backend')

@section('title', 'Data Sekolah')
@section('page-title', 'Data Sekolah')

@section('content')

    <x-ui.page-header
        title="Data Sekolah"
        subtitle="Informasi profil sekolah yang tampil di website publik"
    />

    <form action="{{ route('admin.school.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            {{-- KOLOM KIRI — Upload File --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Logo Sekolah --}}
                <x-ui.card title="Logo Sekolah">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            @if ($school->logo)
                                <img id="logo-preview" src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
                                     class="w-full h-full object-contain p-2">
                            @else
                                <div id="logo-placeholder" class="text-center">
                                    <i data-lucide="image" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                    <p class="text-xs text-surface-400 mt-1">Belum ada logo</p>
                                </div>
                                <img id="logo-preview" src="" alt="" class="w-full h-full object-contain p-2 hidden">
                            @endif
                        </div>

                        <label for="logo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                {{ $school->logo ? 'Ganti Logo' : 'Pilih Logo' }}
                            </span>
                            <input type="file" id="logo" name="logo" accept="image/*"
                                   class="hidden" onchange="previewImage(event, 'logo')">
                        </label>

                        <p class="text-xs text-surface-400 mt-2 text-center">PNG, JPG, SVG, WebP. Maks 2MB.</p>
                        @error('logo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>

                {{-- Foto Kepala Sekolah --}}
                <x-ui.card title="Foto Kepala Sekolah">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            @if ($school->principal_photo)
                                <img id="principal-preview" src="{{ asset('storage/' . $school->principal_photo) }}"
                                     alt="Foto Kepsek" class="w-full h-full object-cover">
                            @else
                                <div id="principal-placeholder" class="text-center">
                                    <i data-lucide="user-circle" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                    <p class="text-xs text-surface-400 mt-1">Belum ada foto</p>
                                </div>
                                <img id="principal-preview" src="" alt="" class="w-full h-full object-cover hidden">
                            @endif
                        </div>

                        <label for="principal_photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                {{ $school->principal_photo ? 'Ganti Foto' : 'Pilih Foto' }}
                            </span>
                            <input type="file" id="principal_photo" name="principal_photo" accept="image/*"
                                   class="hidden" onchange="previewImage(event, 'principal')">
                        </label>
                        <p class="text-xs text-surface-400 mt-2 text-center">JPG, PNG. Maks 2MB.</p>
                        @error('principal_photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>

                {{-- Foto Struktur Organisasi --}}
                <x-ui.card title="Foto Struktur Organisasi">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300 flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            @if ($school->structure_image)
                                <img id="structure-preview" src="{{ asset('storage/' . $school->structure_image) }}"
                                     alt="Struktur" class="w-full h-full object-contain">
                            @else
                                <div id="structure-placeholder" class="text-center">
                                    <i data-lucide="network" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                    <p class="text-xs text-surface-400 mt-1">Belum ada gambar</p>
                                </div>
                                <img id="structure-preview" src="" alt="" class="w-full h-full object-contain hidden">
                            @endif
                        </div>

                        <label for="structure_image" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                {{ $school->structure_image ? 'Ganti Gambar' : 'Pilih Gambar' }}
                            </span>
                            <input type="file" id="structure_image" name="structure_image" accept="image/*"
                                   class="hidden" onchange="previewImage(event, 'structure')">
                        </label>
                        <p class="text-xs text-surface-400 mt-2 text-center">JPG, PNG. Maks 3MB.</p>
                        @error('structure_image') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>
            </div>

            {{-- KOLOM KANAN — Data --}}
            <div class="lg:col-span-2 space-y-5">

                {{-- Informasi Umum --}}
                <x-ui.card title="Informasi Umum">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-ui.input name="school_name" label="Nama Sekolah"
                                        :value="old('school_name', $school->school_name)" :required="true" />
                        </div>

                        <x-ui.input name="npsn" label="NPSN" :value="old('npsn', $school->npsn)" />
                        <x-ui.input name="nss"  label="NSS"  :value="old('nss', $school->nss)" />

                        <x-ui.input name="accreditation" label="Akreditasi"
                                    :value="old('accreditation', $school->accreditation)" />

                        <x-ui.input name="education_level" label="Jenjang"
                                    :value="old('education_level', $school->education_level)"
                                    placeholder="SMK / SMA / SMP" />

                        <x-ui.input name="status" label="Status"
                                    :value="old('status', $school->status)"
                                    placeholder="Negeri / Swasta" />

                        <x-ui.input name="study_hours" label="Jam Belajar"
                                    :value="old('study_hours', $school->study_hours)"
                                    placeholder="8 Jam" />

                        <x-ui.input name="principal_name" label="Nama Kepala Sekolah"
                                    :value="old('principal_name', $school->principal_name)" />

                        <x-ui.input name="phone" label="Telepon" :value="old('phone', $school->phone)" />
                        <x-ui.input name="fax"   label="Fax"     :value="old('fax', $school->fax)" />

                        <x-ui.input name="email" label="Email" type="email"
                                    :value="old('email', $school->email)" />

                        <x-ui.input name="postal_code" label="Kode Pos"
                                    :value="old('postal_code', $school->postal_code)" />

                        <div class="md:col-span-2">
                            <x-ui.textarea name="address" label="Alamat" rows="2">{{ old('address', $school->address) }}</x-ui.textarea>
                        </div>

                        <x-ui.input name="latitude" label="Lintang (Latitude)"
                                    :value="old('latitude', $school->latitude)"
                                    placeholder="-7.5905..." />

                        <x-ui.input name="longitude" label="Bujur (Longitude)"
                                    :value="old('longitude', $school->longitude)"
                                    placeholder="110.9504..." />

                        <x-ui.input name="elevation" label="Ketinggian (mdpl)"
                                    :value="old('elevation', $school->elevation)"
                                    placeholder="156" />
                    </div>
                </x-ui.card>

                {{-- Deskripsi --}}
                <x-ui.card title="Deskripsi Sekolah">
                    <x-ui.textarea name="description" label="Tentang Sekolah" rows="4">{{ old('description', $school->description) }}</x-ui.textarea>
                </x-ui.card>

                {{-- Visi Misi --}}
                <x-ui.card title="Visi & Misi">
                    <div class="space-y-4">
                        <x-ui.textarea name="vision" label="Visi" rows="3"
                            placeholder="Visi sekolah...">{{ old('vision', $school->vision) }}</x-ui.textarea>

                        <x-ui.textarea name="mission" label="Misi (satu per baris)" rows="6"
                            placeholder="Menanamkan keimanan...&#10;Mewujudkan lulusan...">{{ old('mission', $school->mission) }}</x-ui.textarea>
                        <p class="text-xs text-surface-400">Tulis setiap misi di baris baru (tekan Enter).</p>
                    </div>
                </x-ui.card>

                {{-- Sambutan Kepala Sekolah --}}
                <x-ui.card title="Sambutan Kepala Sekolah">
                    <x-ui.textarea name="principal_message" label="Isi Sambutan" rows="6"
                        placeholder="Assalamualaikum warahmatullahi wabarakatuh...">{{ old('principal_message', $school->principal_message) }}</x-ui.textarea>
                </x-ui.card>

                {{-- Sejarah --}}
                <x-ui.card title="Sejarah Sekolah">
                    <x-ui.textarea name="history" label="Sejarah" rows="8"
                        placeholder="Sejarah berdirinya sekolah...">{{ old('history', $school->history) }}</x-ui.textarea>
                </x-ui.card>

            </div>
        </div>

        {{-- Action Bar --}}
        <div class="flex items-center justify-end gap-2 mt-6">
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="save" :loading="true">
                Simpan Perubahan
            </x-ui.button>
        </div>

    </form>

@endsection

@push('scripts')
<script>
    function previewImage(event, prefix) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById(prefix + '-preview');
            const placeholder = document.getElementById(prefix + '-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush