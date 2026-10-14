@extends('layouts.public')

@section('title', 'Struktur Organisasi')

@section('content')

<x-public.profile._header
    title="Struktur Organisasi"
    subtitle="Susunan manajemen dan tata kelola sekolah"
    active="struktur"
/>

<section class="py-12 -mt-8 relative z-10">
    <div class="max-w-5xl mx-auto px-6">

        @if ($school?->structure_image)
            <div class="bg-white rounded-2xl border border-surface-200/70 p-4 lg:p-6
                        shadow-xl shadow-primary-900/5 hover:shadow-2xl hover:shadow-primary-900/10
                        transition-all duration-300 animate-fade-in-up">
                <div class="rounded-xl overflow-hidden bg-surface-50">
                    <img src="{{ asset('storage/' . $school->structure_image) }}"
                         alt="Struktur Organisasi"
                         class="w-full h-auto object-contain">
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-surface-200/70 p-12
                        shadow-xl shadow-primary-900/5 text-center animate-fade-in-up">
                <div class="w-20 h-20 rounded-2xl bg-primary-100 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="network" class="w-10 h-10 text-primary-600"></i>
                </div>
                <h3 class="text-lg font-bold text-surface-900 mb-2">Struktur Organisasi Belum Tersedia</h3>
                <p class="text-sm text-surface-500 max-w-md mx-auto">
                    Informasi struktur organisasi akan ditampilkan di sini setelah diunggah oleh administrator.
                </p>
            </div>
        @endif

    </div>
</section>

@endsection