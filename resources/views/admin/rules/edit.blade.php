@extends('layouts.backend')

@section('title', 'Edit Tata Tertib')
@section('page-title', 'Tata Tertib')

@section('content')

    <x-ui.page-header title="Edit Tata Tertib" subtitle="Perbarui aturan" />

    <form action="{{ route('admin.rules.update', $rule) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')

        <x-ui.card title="Detail Tata Tertib">
            <div class="space-y-4">
                <x-ui.input name="title" label="Judul" :value="old('title', $rule->title)" :required="true" icon="clipboard-list" />
                <x-ui.textarea name="content" label="Konten" rows="10" :required="true">{{ old('content', $rule->content) }}</x-ui.textarea>
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rule->is_active))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Aktif</span>
                    </label>
                </div>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.rules.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
        </div>
    </form>

@endsection