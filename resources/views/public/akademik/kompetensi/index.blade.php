@extends('layouts.public')

@section('title', 'Kompetensi Keahlian ')

@section('content')

    <section class="relative text-white overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('images/fotosekolah.webp') }}" onerror="this.style.display='none';" alt=""
                class="w-full h-full object-cover blur-[2px] brightness-[0.55]">
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
        </div>
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">
            <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
                <span class="text-white font-medium">Kompetensi Keahlian</span>
            </nav>

            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight drop-shadow-lg">
                Kompetensi
                <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                    Keahlian
                </span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 drop-shadow">
                Program keahlian unggulan yang siap mencetak lulusan kompeten di dunia industri.
            </p>
        </div>

        <x-public.wave />
    </section>

    <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        @php
            $palette = [
                ['bg' => '#35B768', 'light' => '#F3EFFB', 'icon' => 'code'],
                ['bg' => '#3589BD', 'light' => '#E7F1F8', 'icon' => 'network'],
                ['bg' => '#CF4141', 'light' => '#FEF4E7', 'icon' => 'palette'],
                ['bg' => '#EA580C', 'light' => '#E7F6EC', 'icon' => 'calculator'],
                ['bg' => '#CF4141', 'light' => '#FBEBEB', 'icon' => 'briefcase'],
                ['bg' => '#0EA5E9', 'light' => '#E5F6FD', 'icon' => 'cpu'],
            ];
        @endphp

        @if ($departments->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl border border-surface-200/70">
                <i data-lucide="layers" class="w-12 h-12 text-surface-400 mx-auto mb-3"></i>
                <p class="text-surface-500">Belum ada data jurusan.</p>
            </div>
        @else
            {{-- GRID: items-stretch wajib supaya card meregang sama tinggi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
                @foreach ($departments as $dept)
                    @php $c = $palette[$loop->index % count($palette)]; @endphp

                    {{-- WRAPPER: h-full + flex flex-col supaya card bisa mengisi --}}
                    <a href="{{ route('public.akademik.kompetensi.show', $dept->slug) }}"
                        class="group relative scroll-reveal flex flex-col h-full" data-delay="{{ ($loop->index % 4) * 100 }}">

                        {{-- Illustration (tinggi tetap) --}}
                        <div class="relative h-[175px] flex items-end justify-center z-10 px-4 shrink-0">
                            @if ($dept->image)
                                <img src="{{ asset('storage/' . $dept->image) }}" alt="{{ $dept->name }}"
                                    class="max-h-[160px] max-w-full object-contain drop-shadow-xl
                                        transition-all duration-500 group-hover:-translate-y-3 group-hover:scale-105">
                            @else
                                <div class="w-[130px] h-[130px] rounded-3xl flex items-center justify-center
                                        shadow-lg transition-all duration-500 group-hover:-translate-y-3 group-hover:scale-105"
                                    style="background: linear-gradient(135deg, {{ $c['light'] }}, {{ $c['bg'] }}20);">
                                    <i data-lucide="{{ $c['icon'] }}" class="w-16 h-16"
                                        style="color: {{ $c['bg'] }};"></i>
                                </div>
                            @endif
                        </div>

                        {{-- CARD: flex-1 + min-h supaya tinggi seragam --}}
                        <div class="relative rounded-3xl -mt-8 pt-14 pb-6 px-6 flex-1 flex flex-col min-h-[240px] text-white
                                transition-all duration-500 group-hover:shadow-2xl group-hover:-translate-y-1"
                            style="background: linear-gradient(135deg, {{ $c['bg'] }}, {{ $c['bg'] }}dd);">
                            <div class="absolute inset-0 rounded-3xl opacity-[0.08] pointer-events-none"
                                style="background-image: radial-gradient(circle at 20% 20%, white 1px, transparent 1px); background-size: 20px 20px;">
                            </div>

                            {{-- KONTEN: flex-1 flex flex-col supaya CTA selalu di bawah --}}
                            <div class="relative flex-1 flex flex-col">
                                <div
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full self-start
                                        bg-white/20 backdrop-blur-sm text-[10px] font-bold uppercase tracking-widest mb-3">
                                    {{ $dept->code }}
                                </div>

                                <h3 class="text-lg font-bold leading-tight line-clamp-2">{{ $dept->name }}</h3>

                                {{-- Deskripsi: flex-1 + line-clamp-3 supaya tinggi tidak berubah --}}
                                <p class="text-xs leading-relaxed mt-3 opacity-90 line-clamp-3 flex-1">
                                    {{ $dept->description }}
                                </p>

                                <div
                                    class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold
                                        opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    Selengkapnya
                                    <i data-lucide="arrow-right"
                                        class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </section>

@endsection
