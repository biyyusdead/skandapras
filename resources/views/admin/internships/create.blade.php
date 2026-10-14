@extends('layouts.backend')

@section('title', 'Tambah PKL & BKK')
@section('page-title', 'PKL & BKK')

@section('content')

    <x-ui.page-header title="Tambah Data PKL & BKK" subtitle="Tambahkan informasi PKL atau BKK" />

    <form action="{{ route('admin.internships.store') }}" method="POST" class="space-y-5">
        @csrf

        <x-ui.card title="Informasi">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <x-ui.input name="title" label="Judul" :value="old('title')" :required="true" icon="briefcase" />
                </div>
                <x-ui.input name="company" label="Perusahaan/Mitra" :value="old('company')" icon="building" />
                <div></div>
                <x-ui.input name="start_date" label="Tanggal Mulai" type="date" :value="old('start_date')" icon="calendar" />
                <x-ui.input name="end_date" label="Tanggal Selesai" type="date" :value="old('end_date')" icon="calendar" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="description" label="Deskripsi" rows="5"
                        placeholder="Deskripsi PKL/BKK...">{{ old('description') }}</x-ui.textarea>
                </div>
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

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.internships.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

@endsection