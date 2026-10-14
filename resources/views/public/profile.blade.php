@extends('layouts.public')

@section('title', 'Tentang Sekolah')
@section('description', 'Profil dan informasi lengkap ' . ($school?->school_name ?? 'sekolah'))

@section('content')

{{-- ============================================================ --}}
{{-- PAGE TITLE --}}
{{-- ============================================================ --}}
<section class="bg-gradient-to-br from-primary-50 to-white border-b border-surface-100">
    <div class="max-w-5xl mx-auto px-6 py-12 lg:py-16">
        <div class="flex items-center gap-2 text-xs text-surface-500 mb-4">
            <a href="{{ url('/') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="text-surface-700 font-medium">Tentang Sekolah</span>
        </div>

        <h1 class="text-3xl lg:text-4xl font-bold text-surface-900 tracking-tight">
            {{ $school?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
        </h1>
        <p class="text-sm text-surface-500 mt-2 max-w-2xl">
            Mengenal lebih dekat identitas, visi misi, dan sejarah sekolah kami.
        </p>
    </div>
</section>

{{-- ============================================================ --}}
{{-- QUOTE / TAGLINE --}}
{{-- ============================================================ --}}
<section class="py-12">
    <div class="max-w-4xl mx-auto px-6">
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-8 lg:p-10
                    flex flex-col sm:flex-row items-center gap-6">

            <div class="shrink-0">
                @if ($school?->logo)
                    <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo"
                         class="w-24 h-24 object-contain">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-primary-100 flex items-center justify-center">
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
{{-- IDENTITAS SEKOLAH --}}
{{-- ============================================================ --}}
<section id="identitas" class="py-12 bg-surface-50">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Identitas</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Identitas Sekolah</h2>
        </div>

        @php
            $identitas = [
                ['NPSN',            $school?->npsn],
                ['NSS',             $school?->nss],
                ['Nama Sekolah',    $school?->school_name],
                ['Jenjang',         $school?->education_level],
                ['Status',          $school?->status],
                ['Akreditasi',      $school?->accreditation],
                ['Alamat',          $school?->address],
                ['Kode Pos',        $school?->postal_code],
                ['Telepon',         $school?->phone],
                ['Fax',             $school?->fax],
                ['Email',           $school?->email],
                ['Jam Belajar',     $school?->study_hours],
            ];
        @endphp

        <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden shadow-sm">
            <dl class="divide-y divide-surface-100">
                @foreach ($identitas as [$label, $value])
                    @if ($value)
                        <div class="grid grid-cols-1 sm:grid-cols-[180px_1fr] gap-1 sm:gap-6 px-6 py-3.5
                                    hover:bg-surface-50/60 transition-colors">
                            <dt class="text-sm font-medium text-surface-500">{{ $label }}</dt>
                            <dd class="text-sm text-surface-800">{{ $value }}</dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- VISI & MISI --}}
{{-- ============================================================ --}}
@if ($school?->vision || $school?->mission)
<section class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Arah & Tujuan</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Visi & Misi</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Visi --}}
            @if ($school?->vision)
                <div class="relative bg-gradient-to-br from-primary-600 to-primary-800
                            rounded-2xl p-7 overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary-400/20 rounded-full blur-3xl"></div>

                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20
                                    flex items-center justify-center mb-4">
                            <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-3">Visi</h3>
                        <p class="text-sm text-white/95 leading-relaxed">
                            {{ $school->vision }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Misi --}}
            @if ($school?->mission)
                <div class="relative bg-gradient-to-br from-primary-700 to-primary-900
                            rounded-2xl p-7 overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary-400/20 rounded-full blur-3xl"></div>

                    <div class="relative">
                        <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20
                                    flex items-center justify-center mb-4">
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
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- SAMBUTAN KEPALA SEKOLAH --}}
{{-- ============================================================ --}}
@if ($school?->principal_message)
<section id="sambutan" class="py-12 bg-surface-50">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Sambutan</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Sambutan Kepala Sekolah</h2>
        </div>

        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-8 lg:p-10">
            <div class="flex flex-col sm:flex-row items-start gap-6 mb-6">

                @if ($school?->principal_photo)
                    <img src="{{ asset('storage/' . $school->principal_photo) }}" alt="{{ $school->principal_name }}"
                         class="w-24 h-24 rounded-2xl object-cover shrink-0 ring-1 ring-surface-200">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200
                                flex items-center justify-center shrink-0">
                        <i data-lucide="user-circle" class="w-12 h-12 text-primary-600"></i>
                    </div>
                @endif

                <div>
                    <p class="text-base font-bold text-surface-900">{{ $school->principal_name ?? '-' }}</p>
                    <p class="text-xs text-surface-500 mt-1">Kepala Sekolah</p>
                    <p class="text-xs text-primary-600 font-medium mt-0.5">
                        {{ $school->school_name }}
                    </p>
                </div>
            </div>

            <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                {{ $school->principal_message }}
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- SEJARAH --}}
{{-- ============================================================ --}}
@if ($school?->history)
<section id="sejarah" class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Perjalanan</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Sejarah Sekolah</h2>
        </div>

        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-8 lg:p-10
                    relative overflow-hidden">
            <div class="absolute top-0 left-8 w-px h-full bg-gradient-to-b from-primary-200 via-primary-100 to-transparent"></div>

            <div class="relative pl-8">
                <div class="w-3 h-3 rounded-full bg-primary-600 ring-4 ring-primary-100 absolute -left-1.5 top-1"></div>
                <p class="text-sm text-surface-700 leading-relaxed whitespace-pre-line">
                    {{ $school->history }}
                </p>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ============================================================ --}}
{{-- STRUKTUR ORGANISASI --}}
{{-- ============================================================ --}}
<section id="struktur" class="py-12 bg-surface-50">
    <div class="max-w-4xl mx-auto px-6">

        <div class="mb-8">
            <p class="text-xs font-semibold text-primary-600 uppercase tracking-widest mb-2">Manajemen</p>
            <h2 class="text-2xl font-bold text-surface-900 tracking-tight">Struktur Organisasi</h2>
        </div>

        @php
            $struktur = [
                ['Kepala Sekolah',        $school?->principal_name,                    'user-tie',  'primary'],
                ['Wakil Kepala Sekolah',  'Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas', 'users',     'indigo'],
                ['Kepala Tata Usaha',     'Kepala TU & Staf Administrasi',               'briefcase', 'green'],
                ['Kepala Program Studi',  'Ka. Prodi RPL, TKJ, MM, AKL, OTKP',          'layers',    'yellow'],
                ['Guru & Karyawan',       'Tenaga Pendidik & Kependidikan',             'user-check', 'purple'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach ($struktur as $item)
                @php
                    [$role, $desc, $icon, $color] = $item;
                    $colorMap = [
                        'primary' => ['bg' => 'bg-primary-100', 'text' => 'text-primary-600', 'ring' => 'ring-primary-200'],
                        'indigo'  => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-600',  'ring' => 'ring-indigo-200'],
                        'green'   => ['bg' => 'bg-green-100',   'text' => 'text-green-600',   'ring' => 'ring-green-200'],
                        'yellow'  => ['bg' => 'bg-yellow-100',  'text' => 'text-yellow-600',  'ring' => 'ring-yellow-200'],
                        'purple'  => ['bg' => 'bg-purple-100',  'text' => 'text-purple-600',  'ring' => 'ring-purple-200'],
                    ];
                    $c = $colorMap[$color];
                @endphp

                <div class="bg-white rounded-2xl border border-surface-200/70 p-5
                            flex items-center gap-4 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl {{ $c['bg'] }} {{ $c['text'] }}
                                flex items-center justify-center shrink-0 ring-1 {{ $c['ring'] }}">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-surface-900">{{ $role }}</p>
                        <p class="text-xs text-surface-500 mt-0.5">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection