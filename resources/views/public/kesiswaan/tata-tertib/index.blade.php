@extends('layouts.public')

@section('title', 'Tata Tertib ')

@section('content')

<section class="relative overflow-hidden text-white">

    {{-- Background Image + Blur --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';"
             alt=""
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">

        {{-- Breadcrumb --}}
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5 animate-fade-in-up">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Tata Tertib</span>
        </nav>

        {{-- Title --}}
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            Tata Tertib
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Siswa
            </span>
        </h1>

        {{-- Subtitle --}}
        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Aturan dan pedoman yang berlaku bagi seluruh siswa SMK Negeri 2 Karanganyar.
        </p>
    </div>

    <x-public.wave />
</section>

<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    @if ($rules->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
            <i data-lucide="clipboard-list" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
            <p class="text-surface-500">Belum ada data tata tertib.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach ($rules as $i => $rule)
                <div class="bg-white rounded-2xl border border-surface-200/70 shadow-lg overflow-hidden
                            scroll-reveal" data-delay="{{ $i * 80 }}">

                    {{-- Header — SOLID tanpa gradient --}}
                    <div class="bg-primary-700 p-5 md:p-6 text-white">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur-sm ring-1 ring-white/20
                                        flex items-center justify-center shrink-0">
                                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-xl font-bold leading-tight">{{ $rule->title }}</h2>
                                <p class="text-xs text-primary-100/80 mt-1 uppercase tracking-wider">
                                    Tata Tertib Sekolah
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 md:p-8">
                        <div class="prose prose-sm max-w-none text-surface-700 leading-relaxed whitespace-pre-line">
                            {{ $rule->content }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</section>

@endsection

@push('styles')
<style>
    .scroll-reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s cubic-bezier(0.16,1,0.3,1), transform 0.6s cubic-bezier(0.16,1,0.3,1); }
    .scroll-reveal.show { opacity: 1; transform: translateY(0); }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
    });
</script>
@endpush