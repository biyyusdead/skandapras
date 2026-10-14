@extends('layouts.backend')

@section('title', 'Tambah Info SPMB')
@section('page-title', 'SPMB')

@section('content')

    <x-ui.page-header title="Tambah Info SPMB" subtitle="Tambahkan informasi SPMB / PPDB" />

    <form action="{{ route('admin.admissions.store') }}" method="POST" class="space-y-5">
        @csrf

        <x-ui.card title="Informasi Umum">
            <div class="space-y-4">
                <x-ui.input name="title" label="Judul" :value="old('title')" :required="true" icon="graduation-cap"
                            placeholder="Contoh: PPDB / SPMB 2025/2026" />
                <x-ui.textarea name="description" label="Deskripsi Singkat" rows="3"
                    placeholder="Deskripsi singkat SPMB...">{{ old('description') }}</x-ui.textarea>
                <x-ui.input name="official_link" label="Link Resmi PPDB Jawa Tengah" type="url"
                            :value="old('official_link')" icon="external-link"
                            placeholder="https://ppdb.jatengprov.go.id" />
            </div>
        </x-ui.card>

        <x-ui.card title="Detail Informasi">
            <div class="space-y-4">
                <x-ui.textarea name="requirements" label="Persyaratan" rows="5"
                    placeholder="1. Lulusan SMP/MTs&#10;2. Memiliki ijazah...">{{ old('requirements') }}</x-ui.textarea>
                <x-ui.textarea name="schedule" label="Jadwal Penting" rows="4"
                    placeholder="Pendaftaran: ...&#10;Verifikasi: ...">{{ old('schedule') }}</x-ui.textarea>
                <x-ui.textarea name="additional_information" label="Informasi Tambahan" rows="4"
                    placeholder="Informasi tambahan...">{{ old('additional_information') }}</x-ui.textarea>
            </div>
        </x-ui.card>

        <x-ui.card>
            <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                          hover:border-primary-400 transition-colors duration-150">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                       class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                <span class="text-sm text-surface-700">Tampilkan di halaman publik</span>
            </label>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.admissions.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

@endsection