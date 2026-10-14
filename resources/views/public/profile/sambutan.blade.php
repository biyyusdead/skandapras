@extends('layouts.public')

@section('title', 'Sambutan Kepala Sekolah')

@section('content')

<x-public.profile._header
    title="Sambutan Kepala Sekolah"
    subtitle="Pesan dan harapan dari pimpinan sekolah"
    active="sambutan"
/>

<section class="py-12 -mt-8 relative z-10">
    <div class="max-w-4xl mx-auto px-6">

        <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                    shadow-xl shadow-primary-900/5 hover:shadow-2xl hover:shadow-primary-900/10
                    transition-all duration-300 animate-fade-in-up">

            {{-- Foto Kepala Sekolah --}}
            @if ($school?->principal_photo)
                <div class="relative aspect-[16/9] sm:aspect-[21/9] bg-surface-100 overflow-hidden group">
                    <img src="{{ asset('storage/' . $school->principal_photo) }}"
                         alt="{{ $school->principal_name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-900/90 via-primary-900/30 to-transparent"></div>

                    {{-- Name overlay --}}
                    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8">
                        <p class="text-xs font-semibold text-primary-200 uppercase tracking-widest mb-1">
                            Kepala Sekolah
                        </p>
                        <h2 class="text-xl lg:text-2xl font-bold text-white">
                            {{ $school->principal_name }}
                        </h2>
                        <p class="text-sm text-primary-100/80 mt-1">
                            {{ $school->school_name }}
                        </p>
                    </div>
                </div>
            @else
                <div class="relative bg-gradient-to-br from-primary-600 to-primary-800 p-10 text-center">
                    <div class="w-24 h-24 rounded-2xl bg-white/10 ring-1 ring-white/20 backdrop-blur-sm
                                flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="user-circle" class="w-12 h-12 text-white"></i>
                    </div>
                    <p class="text-xs font-semibold text-primary-200 uppercase tracking-widest mb-1">
                        Kepala Sekolah
                    </p>
                    <h2 class="text-xl font-bold text-white">{{ $school->principal_name ?? '-' }}</h2>
                </div>
            @endif

            {{-- Content --}}
            <div class="p-8 lg:p-10">
                <div class="flex items-start gap-4 mb-6">
                    <i data-lucide="quote" class="w-10 h-10 text-primary-200 shrink-0"></i>
                    <div class="h-px flex-1 bg-gradient-to-r from-primary-200 to-transparent mt-5"></div>
                </div>

                <div class="prose prose-sm max-w-none text-surface-700 leading-loose whitespace-pre-line text-[15px]">
                    {{ $school?->principal_message ?? 'Sambutan kepala sekolah belum tersedia.' }}
                </div>
            </div>
        </div>
    </div>
</section>

@endsection