@extends('layouts.backend')

@section('title', 'Tambah User')
@section('page-title', 'Data Users')

@section('content')

    <x-ui.page-header
        title="Tambah User"
        subtitle="Buat akun pengguna baru"
    />

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-5">
        @csrf

        <x-ui.card title="Informasi Akun">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2">
                    <x-ui.input name="name" label="Nama Lengkap" :value="old('name')" :required="true"
                                placeholder="Contoh: Budi Santoso" />
                </div>

                <x-ui.input name="email" label="Email" type="email" icon="mail"
                            :value="old('email')" :required="true"
                            placeholder="nama@sekolah.sch.id" />

                <x-ui.input name="phone" label="No. Telepon" icon="phone"
                            :value="old('phone')" placeholder="08xxxxxxxxxx" />

                <x-ui.input name="password" label="Password" type="password" icon="lock"
                            :required="true" hint="Minimal 6 karakter" />

                <x-ui.input name="password_confirmation" label="Konfirmasi Password" type="password" icon="lock"
                            :required="true" />

                <x-ui.select
                    name="role"
                    label="Role"
                    :options="collect($roles)->mapWithKeys(fn($r) => [$r->value => $r->label()])->toArray()"
                    :required="true"
                    :value="old('role')"
                />

                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-1.5">Status</label>
                    <label class="inline-flex items-center gap-2 px-3 py-2 border border-surface-300 rounded-lg cursor-pointer
                                  hover:border-primary-400 transition-colors duration-150">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', true))
                               class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30">
                        <span class="text-sm text-surface-700">Aktif</span>
                    </label>
                </div>

            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('admin.users.index') }}" variant="secondary">
                Batal
            </x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">
                Simpan
            </x-ui.button>
        </div>

    </form>

@endsection