@extends('layouts.backend')

@section('title', 'Edit Kategori')
@section('page-title', 'Data Kategori Barang')

@section('content')

    <x-ui.page-header title="Edit Kategori" subtitle="Perbarui data kategori" />

    <form action="{{ route('waka.categories.update', $category) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <x-ui.card title="Informasi Kategori">
            <div class="space-y-4">
                <x-ui.input name="name" label="Nama Kategori" :value="old('name', $category->name)" :required="true" icon="tags" />
                <x-ui.textarea name="description" label="Deskripsi" rows="4">{{ old('description', $category->description) }}</x-ui.textarea>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.categories.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
        </div>
    </form>

@endsection