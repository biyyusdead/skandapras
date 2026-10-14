@extends('layouts.public')

@section('title', 'Identitas Sekolah')
@section('description', 'Identitas resmi ' . ($school?->school_name ?? 'sekolah'))

@section('content')

<x-public.profile._header
    title="{{ $school?->school_name ?? 'SMK Negeri 2 Karanganyar' }}"
    subtitle="Mengenal lebih dekat identitas sekolah kami"
    active="index"
/>

{{-- ============================================================ --}}
{{-- QUOTE / TAGLINE --}}
{{-- ============================================================ --}}
<section class="py-12 -mt-8 relative z-10">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-xl shadow-primary-900/5
                    p-8 lg:p-10 flex flex-col sm:flex-row items-center gap-6
                    hover:shadow-2xl hover:shadow-primary-900/10 transition-all duration-300
                    animate-fade-in-up">

            <div class="shrink-0 relative">
                <div class="absolute inset-0 bg-primary-400/30 rounded-full blur-xl"></div>
                @if ($school?->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
                         class="relative w-24 h-24 object-contain drop-shadow-lg">
                @else
                    <div class="relative w-24 h-24 rounded-2xl bg-primary-100 flex items-center justify-center">
                        <i data-lucide="graduation-cap" class="w-10 h-10 text-primary-600"></i>
                    </div>
                @endif
            </div>

            <div class="text-center sm:text-left">
                <i data-lucide="quote" class="w-6 h-6 text-primary-300 mb-2 mx-auto sm:mx-0"></i>
                <p class="text-lg font-medium text-surface-800 italic leading-relaxed">
                    "SMK Bisa, SMK Hebat, SMK Bisa Hebat,
                    {{ $school?->school_name ?? 'SMK Negeri 2 Karanganyar' }} Pasti Bisa!"
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- IDENTITAS --}}
{{-- ============================================================ --}}
<section class="pb-16">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8 animate-fade-in-up">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Identitas</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Data Sekolah</h2>
        </div>

        @php
            $identitas = [
                ['icon' => 'hash',        'label' => 'NPSN',         'value' => $school?->npsn],
                ['icon' => 'hash',        'label' => 'NSS',          'value' => $school?->nss],
                ['icon' => 'school',      'label' => 'Nama Sekolah', 'value' => $school?->school_name],
                ['icon' => 'layers',      'label' => 'Jenjang',      'value' => $school?->education_level],
                ['icon' => 'building',    'label' => 'Status',       'value' => $school?->status],
                ['icon' => 'award',       'label' => 'Akreditasi',   'value' => $school?->accreditation],
                ['icon' => 'map-pin',     'label' => 'Alamat',       'value' => $school?->address],
                ['icon' => 'mailbox',     'label' => 'Kode Pos',     'value' => $school?->postal_code],
                ['icon' => 'phone',       'label' => 'Telepon',      'value' => $school?->phone],
                ['icon' => 'printer',     'label' => 'Fax',          'value' => $school?->fax],
                ['icon' => 'mail',        'label' => 'Email',        'value' => $school?->email],
                ['icon' => 'clock',       'label' => 'Jam Belajar',  'value' => $school?->study_hours],
            ];
        @endphp

        <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                    shadow-lg shadow-primary-900/5 hover:shadow-xl hover:shadow-primary-900/10
                    transition-all duration-300 animate-fade-in-up stagger-1">
            <dl class="divide-y divide-surface-100">
                @foreach ($identitas as $row)
                    @if ($row['value'])
                        <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] gap-1 sm:gap-6 px-6 py-4
                                    hover:bg-primary-50/40 transition-colors duration-200 group">
                            <dt class="flex items-center gap-2.5 text-sm font-medium text-surface-500">
                                <div class="w-7 h-7 rounded-lg bg-primary-50 group-hover:bg-primary-100
                                            flex items-center justify-center shrink-0 transition-colors">
                                    <i data-lucide="{{ $row['icon'] }}" class="w-3.5 h-3.5 text-primary-600"></i>
                                </div>
                                {{ $row['label'] }}
                            </dt>
                            <dd class="text-sm text-surface-800 leading-relaxed sm:pl-0 pl-9">{{ $row['value'] }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>

        {{-- Visi Misi --}}
        @if ($school?->vision || $school?->mission)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-10">

                @if ($school?->vision)
                    <div class="relative bg-gradient-to-br from-primary-600 to-primary-800
                                rounded-2xl p-7 overflow-hidden shadow-xl shadow-primary-900/20
                                hover:shadow-2xl hover:shadow-primary-900/30 transition-shadow duration-300
                                animate-fade-in-up stagger-2 group">
                        <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary-400/20 rounded-full blur-3xl
                                    group-hover:bg-primary-300/30 transition-colors duration-500"></div>

                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20
                                        flex items-center justify-center mb-4
                                        group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-3">Visi</h3>
                            <p class="text-sm text-white/95 leading-relaxed">{{ $school->vision }}</p>
                        </div>
                    </div>
                @endif

                @if ($school?->mission)
                    <div class="relative bg-gradient-to-br from-primary-700 to-primary-900
                                rounded-2xl p-7 overflow-hidden shadow-xl shadow-primary-900/20
                                hover:shadow-2xl hover:shadow-primary-900/30 transition-shadow duration-300
                                animate-fade-in-up stagger-3 group">
                        <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary-400/20 rounded-full blur-3xl
                                    group-hover:bg-primary-300/30 transition-colors duration-500"></div>

                        <div class="relative">
                            <div class="w-12 h-12 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20
                                        flex items-center justify-center mb-4
                                        group-hover:scale-110 transition-transform duration-300">
                                <i data-lucide="target" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-3">Misi</h3>
                            <ol class="space-y-3">
                                @foreach ($school->missions as $i => $m)
                                    <li class="flex items-start gap-2.5 text-sm text-white/95 leading-relaxed">
                                        <span class="w-5 h-5 rounded-full bg-white/15 ring-1 ring-white/20
                                                     flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                            {{ $i + 1 }}
                                        </span>
                                        <span>{{ $m }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>

@endsection