@extends('layouts.public')

@section('title', $product->name . ' — Produk Unggulan')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';" alt=""
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <a href="{{ route('public.akademik.produk.index') }}" class="hover:text-white transition-colors">Produk Unggulan</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium line-clamp-1">{{ $product->name }}</span>
        </nav>

        <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold tracking-tight">
            {{ $product->name }}
        </h1>

        @if ($product->category)
            <p class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white ring-1 ring-white/25">
                <i data-lucide="tag" class="w-3 h-3"></i>
                {{ $product->category }}
            </p>
        @endif
    </div>

    <x-public.wave />
</section>

{{-- CONTENT --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    <div class="grid gap-8 lg:grid-cols-[1.2fr_1fr]">

        {{-- GAMBAR --}}
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm overflow-hidden">
            <div class="relative aspect-[16/10] bg-surface-100">
                @if ($product->image_url)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                        <i data-lucide="package" class="w-20 h-20 text-primary-400"></i>
                    </div>
                @endif

                @if ($product->code)
                    <span class="absolute top-4 right-4 px-3 py-1 rounded-full
                                 bg-primary-600 text-white text-[11px] font-bold uppercase tracking-wider shadow-md">
                        {{ $product->code }}
                    </span>
                @endif
            </div>
        </div>

        {{-- DETAIL --}}
        <div class="space-y-5">

            @if ($product->description)
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-surface-400 mb-3">
                        Deskripsi
                    </h2>
                    <p class="text-sm leading-relaxed text-surface-700 whitespace-pre-line">
                        {{ $product->description }}
                    </p>
                </div>
            @endif

            {{-- Info kontak --}}
            @if ($product->contact)
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-6">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-surface-400 mb-3">
                        Informasi Pemesanan
                    </h2>

                    @php $wa = preg_replace('/[^0-9]/', '', $product->contact); @endphp

                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-600">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-surface-400">Kontak</p>
                            <p class="text-sm font-semibold text-surface-800">{{ $product->contact }}</p>
                        </div>
                    </div>

                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
                       class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl
                              bg-primary-600 hover:bg-primary-700 px-5 py-3 text-sm font-semibold
                              text-white transition-all active:scale-[0.98]">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        Hubungi via WhatsApp
                    </a>
                </div>
            @endif

            {{-- Kembali --}}
            <a href="{{ route('public.akademik.produk.index') }}"
               class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Produk Unggulan
            </a>
        </div>
    </div>

    {{-- PRODUK TERKAIT --}}
    @if ($related->isNotEmpty())
        <div class="mt-14">
            <div class="flex items-end justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-surface-900">Produk Lainnya</h2>
                    <p class="text-sm text-surface-500 mt-1">
                        {{ $product->category ? 'Kategori ' . $product->category : 'Produk unggulan lainnya' }}
                    </p>
                </div>
                <a href="{{ route('public.akademik.produk.index') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700">
                    Lihat semua
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($related as $item)
                    <a href="{{ route('public.akademik.produk.show', $item) }}"
                       class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                              shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 block">

                        <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                    <i data-lucide="package" class="w-12 h-12 text-primary-400"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-bold text-surface-900 line-clamp-2 leading-snug">
                                {{ $item->name }}
                            </h3>
                            @if ($item->description)
                                <p class="mt-2 text-sm text-surface-500 line-clamp-2">{{ $item->description }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</section>

@endsection