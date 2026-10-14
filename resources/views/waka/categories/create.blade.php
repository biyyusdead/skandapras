@extends('layouts.backend')

@section('title', 'Tambah Kategori')
@section('page-title', 'Data Kategori Barang')

@section('content')

    <x-ui.page-header title="Tambah Kategori" subtitle="Buat kategori barang baru" />

    <form action="{{ route('waka.categories.store') }}" method="POST" class="space-y-5">
        @csrf

        <x-ui.card title="Informasi Kategori">
            <div class="space-y-4">
                <x-ui.input name="name" label="Nama Kategori" :value="old('name')" :required="true" icon="tags"
                            placeholder="Contoh: Elektronik, Furnitur, ATK" />
                <x-ui.textarea name="description" label="Deskripsi" rows="4"
                    placeholder="Deskripsi kategori...">{{ old('description') }}</x-ui.textarea>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.categories.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan</x-ui.button>
        </div>
    </form>

@endsection