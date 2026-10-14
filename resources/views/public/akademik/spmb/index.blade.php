@extends('layouts.public')

@section('title', 'SPMB ')

@section('content')

<section class="relative text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';" alt=""
             class="w-full h-full object-cover blur-[2px] brightness-[0.55]">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">SPMB</span>
        </nav>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight drop-shadow-lg">
            {{ $admission->title ?? 'SPMB SMKN 2 Karanganyar' }}
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 drop-shadow">
            {{ $admission->description ?? 'Informasi Sistem Penerimaan Murid Baru' }}
        </p>
    </div>

    <x-public.wave />
</section>

<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-6">

    {{-- Action Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <a href="https://spmb.jatengprov.go.id" target="_blank" rel="noopener"
           class="group bg-white rounded-2xl border border-surface-200/70 p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i data-lucide="external-link" class="w-6 h-6 text-primary-600"></i>
            </div>
            <p class="font-bold text-surface-900">Daftar Sekarang</p>
            <p class="text-xs text-surface-500 mt-1">Website resmi SPMB Jateng</p>
        </a>

        <a href="{{ route('public.akademik.spmb.panduan') }}"
           class="group bg-white rounded-2xl border border-surface-200/70 p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i data-lucide="book-open" class="w-6 h-6 text-primary-600"></i>
            </div>
            <p class="font-bold text-surface-900">Panduan Pendaftaran</p>
            <p class="text-xs text-surface-500 mt-1">Tata cara pendaftaran</p>
        </a>

        <a href="#jadwal"
        class="group bg-white rounded-2xl border border-surface-200/70 p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all">
            <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <i data-lucide="calendar-days" class="w-6 h-6 text-primary-600"></i>
            </div>
            <p class="font-bold text-surface-900">Jadwal Penting</p>
            <p class="text-xs text-surface-500 mt-1">Tanggal & tahapan SPMB</p>
        </a>
    </div>

    {{-- Persyaratan --}}
    @if ($admission?->requirements)
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
            <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                Persyaratan
            </h2>
            <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                {{ $admission->requirements }}
            </div>
        </div>
    @endif

    {{-- Jadwal --}}
    @if ($admission?->schedule)
        <div id="jadwal" class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
            <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                Jadwal Penting
            </h2>
            <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                {{ $admission->schedule }}
            </div>
        </div>
    @endif

    {{-- Info Tambahan --}}
    @if ($admission?->additional_information)
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg p-6 md:p-8">
            <h2 class="text-xl font-bold text-surface-900 mb-4 flex items-center gap-2">
                <div class="w-1 h-6 rounded-full bg-primary-600"></div>
                Informasi Tambahan
            </h2>
            <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                {{ $admission->additional_information }}
            </div>
        </div>
    @endif

    {{-- CTA Website Resmi --}}
    @if ($admission?->official_link)
        <div class="bg-primary-700 text-white rounded-2xl p-6 md:p-8 shadow-xl text-center">
            <h3 class="text-xl font-bold mb-2">Pendaftaran Melalui Website Resmi</h3>
            <p class="text-sm text-primary-100/90 mb-5">
                Proses pendaftaran SPMB Jawa Tengah dilakukan melalui website resmi pemerintah.
            </p>
            <a href="{{ $admission->official_link }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-primary-700
                      hover:bg-primary-50 text-sm font-semibold transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Kunjungi Website SPMB Jateng
            </a>
        </div>
    @endif

</section>

@endsection