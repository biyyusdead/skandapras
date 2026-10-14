@extends('layouts.backend')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')

    <x-ui.page-header title="Pengaturan"
        subtitle="Kelola profil dan keamanan akun Anda" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Info User --}}
        <div class="lg:col-span-1">
            <x-ui.card title="Informasi Akun">
                <div class="flex flex-col items-center text-center py-2">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center text-2xl font-bold text-white mb-3"
                        style="background-color: {{ avatar_color(auth()->user()->name) }};">
                        {{ avatar_initials(auth()->user()->name) }}
                    </div>
                    <p class="font-semibold text-surface-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-surface-500">{{ auth()->user()->email }}</p>
                    <div class="mt-3">
                        <x-ui.badge color="purple">{{ auth()->user()->role->label() }}</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Form --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Profil --}}
            <x-ui.card title="Profil" subtitle="Perbarui informasi dasar akun Anda">
                <form method="POST" action="{{ route('settings.profile') }}" class="space-y-4">
                    @csrf @method('PUT')

                    <x-ui.input name="name" label="Nama Lengkap" icon="user"
                                :value="old('name', auth()->user()->name)" :required="true" />

                    <x-ui.input name="email" label="Email" type="email" icon="mail"
                                :value="old('email', auth()->user()->email)" :required="true" />

                    <x-ui.input name="phone" label="No. Telepon" icon="phone"
                                :value="old('phone', auth()->user()->phone)" placeholder="08xx" />

                    <div class="flex justify-end pt-2">
                        <x-ui.button type="submit" icon="save" :loading="true">Simpan Profil</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            {{-- Password --}}
            <x-ui.card title="Ubah Password" subtitle="Gunakan password yang kuat dan mudah diingat">
                <form method="POST" action="{{ route('settings.password') }}" class="space-y-4">
                    @csrf @method('PUT')

                    <x-ui.input name="current_password" label="Password Saat Ini" type="password" icon="lock"
                                :required="true" />

                    <x-ui.input name="password" label="Password Baru" type="password" icon="lock"
                                :required="true" hint="Minimal 8 karakter" />

                    <x-ui.input name="password_confirmation" label="Konfirmasi Password Baru" type="password" icon="lock"
                                :required="true" />

                    <div class="flex justify-end pt-2">
                        <x-ui.button type="submit" icon="key" :loading="true">Ubah Password</x-ui.button>
                    </div>
                </form>
            </x-ui.card>

        </div>
    </div>

@endsection