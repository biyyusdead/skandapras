@extends('layouts.public')

@section('title', 'Produk Unggulan ')

@section('content')

<section class="relative text-white overflow-hidden">
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';" alt=""
             class="w-full h-full object-cover blur-[2px] brightness-[0.55]">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
    </div>
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Produk Unggulan</span>
        </nav>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight drop-shadow-lg">
            Produk 
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Unggulan
            </span>
        </h1>
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 drop-shadow">
            Karya terbaik siswa SMK Negeri 2 Karanganyar yang siap bersaing di dunia kerja.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- KONTEN ASLI (dibungkus #page-content)                       --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="page-content">

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- Search + Filter --}}
    <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4 mb-8">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                <input type="search" name="search" value="{{ request('search') }}"
                    placeholder="Cari produk unggulan..."
                    class="w-full rounded-xl border border-surface-200 py-3 pl-11 pr-4 text-sm outline-none
                            focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
            </div>

            @if (request()->hasAny(['search', 'category']))
                <a href="{{ route('public.akademik.produk.index') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-surface-200 px-4 py-3
                        text-sm text-surface-600 hover:bg-surface-50 transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Reset
                </a>
            @endif

            <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 hover:bg-primary-700
                        px-5 py-3 text-sm font-medium text-white transition-all active:scale-95">
                <i data-lucide="search" class="w-4 h-4"></i>
                Cari
            </button>
        </form>

        {{-- Filter kategori --}}
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-surface-100">
            <button type="button" data-filter="all"
                    class="filter-btn active inline-flex items-center gap-2 px-4 py-2 rounded-lg
                        border border-surface-200 bg-white text-surface-700 text-sm font-medium
                        hover:border-primary-400 transition-all">
                <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                Semua
            </button>

            @foreach ($categories as $cat)
                <button type="button" data-filter="{{ Str::slug($cat) }}"
                        class="filter-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg
                            border border-surface-200 bg-white text-surface-700 text-sm font-medium
                            hover:border-primary-400 transition-all">
                    {{ $cat }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Grid --}}
    @if ($products->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <i data-lucide="package" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
            <p class="text-surface-500">Belum ada produk unggulan.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="productGrid">
            @foreach ($products as $i => $product)
                <div class="product-card group relative bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                            shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300
                            scroll-reveal"
                    data-category="{{ Str::slug($product->category ?? '') }}"
                    data-delay="{{ ($i % 3) * 100 }}">

                    {{-- Overlay link ke halaman detail --}}
                    <a href="{{ route('public.akademik.produk.show', $product) }}"
                    class="absolute inset-0 z-10"
                    aria-label="Lihat detail {{ $product->name }}"></a>

                    {{-- Image --}}
                    <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                <i data-lucide="package" class="w-16 h-16 text-primary-400"></i>
                            </div>
                        @endif

                        @if ($product->code)
                            <span class="absolute top-3 right-3 z-20 px-2.5 py-1 rounded-full
                                        bg-primary-600 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                {{ $product->code }}
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        @if ($product->category)
                            <span class="inline-block mb-2 rounded-full bg-primary-50 px-2.5 py-0.5
                                        text-[10px] font-semibold uppercase tracking-wider text-primary-700">
                                {{ $product->category }}
                            </span>
                        @endif

                        <h3 class="font-bold text-surface-900 line-clamp-2 leading-snug">
                            {{ $product->name }}
                        </h3>

                        @if ($product->description)
                            <p class="mt-2 text-sm text-surface-500 line-clamp-3">{{$product->description }}</p>
                        @endif

                        @if ($product->contact)
                            <div class="mt-4 pt-4 border-t border-surface-100 relative z-20">
                                <p class="text-xs text-surface-500">
                                    Hubungi:
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $product->contact) }}"
                                    target="_blank" rel="noopener"
                                    class="font-semibold text-primary-600 hover:text-primary-700">
                                        {{ $product->contact }}
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div id="noResult" class="hidden text-center py-20">
            <i data-lucide="package-search" class="w-16 h-16 text-surface-300 mx-auto mb-4"></i>
            <p class="text-surface-500">Tidak ada produk yang cocok.</p>
        </div>
    @endif

</section>

</div> {{-- END: #page-content --}}

{{-- ═══════════════════════════════════════════════════════════ --}}
{{-- SKELETON — Grid card produk (3 kolom)                        --}}
{{-- ═══════════════════════════════════════════════════════════ --}}
<div id="page-skeleton" class="hidden">

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- Filter skeleton --}}
        <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-4 mb-8">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 h-12 animate-shimmer rounded-xl"></div>
                <div class="w-24 h-12 animate-shimmer rounded-xl"></div>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-surface-100">
                <div class="h-9 w-24 animate-shimmer rounded-lg"></div>
                <div class="h-9 w-28 animate-shimmer rounded-lg"></div>
                <div class="h-9 w-24 animate-shimmer rounded-lg"></div>
                <div class="h-9 w-32 animate-shimmer rounded-lg"></div>
            </div>
        </div>

        {{-- Grid skeleton (6 card) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @for ($i = 0; $i < 6; $i++)
                <div class="bg-white rounded-2xl border border-surface-200/70 overflow-hidden">
                    {{-- Image skeleton --}}
                    <div class="aspect-[16/10] animate-shimmer"></div>
                    {{-- Content skeleton --}}
                    <div class="p-5 space-y-3">
                        <div class="h-4 w-20 animate-shimmer rounded-full"></div>
                        <div class="h-5 w-3/4 animate-shimmer rounded"></div>
                        <div class="h-4 w-full animate-shimmer rounded"></div>
                        <div class="h-4 w-2/3 animate-shimmer rounded"></div>
                    </div>
                </div>
            @endfor
        </div>

    </section>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.filter-btn');
        const cards = document.querySelectorAll('.product-card');
        const noRes = document.getElementById('noResult');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.dataset.filter;
                let visible = 0;

                cards.forEach(card => {
                    const match = filter === 'all' || card.dataset.category === filter;
                    card.style.display = match ? '' : 'none';
                    if (match) visible++;
                });

                if (noRes) noRes.classList.toggle('hidden', visible > 0);
            });
        });
    });
</script>
@endpush