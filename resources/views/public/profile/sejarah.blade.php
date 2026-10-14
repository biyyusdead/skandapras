@extends('layouts.public')

@section('title', 'Sejarah Sekolah')

@section('content')

<x-public.profile._header
    title="Sejarah Sekolah"
    subtitle="Perjalanan dan perkembangan sekolah dari masa ke masa"
    active="sejarah"
/>

<section class="py-12 -mt-8 relative z-10">
    <div class="max-w-4xl mx-auto px-6">

        <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                    shadow-xl shadow-primary-900/5 hover:shadow-2xl hover:shadow-primary-900/10
                    transition-all duration-300 animate-fade-in-up">

            {{-- Foto Sekolah --}}
            @if ($school?->logo)
                <div class="relative aspect-[16/9] sm:aspect-[21/9] bg-primary-900 overflow-hidden group">
                    <img src="{{ asset('storage/' . $school->logo) }}"
                         alt="{{ $school->school_name }}"
                         class="w-full h-full object-contain p-8 opacity-90
                                group-hover:scale-105 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary-900/60 to-transparent"></div>
                </div>
            @endif

            {{-- Content --}}
            <div class="p-8 lg:p-10">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center shrink-0">
                        <i data-lucide="history" class="w-6 h-6 text-primary-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-surface-900 leading-tight">
                            Sejarah {{ $school?->school_name ?? 'Sekolah' }}
                        </h2>
                        <p class="text-xs text-surface-500 mt-1">
                            Berdiri sejak tahun 1997
                        </p>
                    </div>
                </div>

                {{-- Timeline content --}}
                <div class="relative pl-6 border-l-2 border-dashed border-primary-200">
                    <div class="absolute -left-[7px] top-2 w-3 h-3 rounded-full bg-primary-600 ring-4 ring-primary-100"></div>

                    <div class="prose prose-sm max-w-none text-surface-700 leading-loose whitespace-pre-line text-[15px]">
                        {{ $school?->history ?? 'Informasi sejarah sekolah belum tersedia.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection