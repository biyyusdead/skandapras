@extends('layouts.backend')

@section('title', 'Edit User')
@section('page-title', 'Data Users')

@section('content')

    <x-ui.page-header
        title="Edit User"
        subtitle="Perbarui data pengguna"
    />

    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <x-ui.card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <x-ui.input name="name" label="Nama Lengkap" :value="old('name', $user->name)" :required="true" />
                </div>

                <x-ui.input name="email" label="Email" type="email" :value="old('email', $user->email)" :required="true" />

                <x-ui.input name="phone" label="No. Telepon" :value="old('phone', $user->phone)" placeholder="08xx" />

                <x-ui.input name="password" label="Password Baru" type="password" hint="Kosongkan jika tidak ingin mengubah" />

                <x-ui.input name="password_confirmation" label="Konfirmasi Password" type="password" />

                <x-ui.select
                    name="role"
                    label="Role"
                    :options="collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->label()])->toArray()"
                    :required="true"
                    :value="old('role', $user->role->value)"
                />

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                    <label class="inline-flex items-center gap-2 mt-2">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', $user->is_active))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-surface-700">Aktif</span>
                    </label>
                </div>

            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2 mt-6">
            <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">
                Batal
            </x-ui.button>
            <x-ui.button type="submit" icon="save">
                Simpan Perubahan
            </x-ui.button>
        </div>

    </form>

@endsection