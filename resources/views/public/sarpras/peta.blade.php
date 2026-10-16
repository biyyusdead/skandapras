@extends('layouts.public')

@section('title', 'Peta Ruangan — SMK Negeri 2 Karanganyar')
@section('description', 'Jelajahi peta interaktif ruangan SMK Negeri 2 Karanganyar. Klik titik untuk melihat detail ruangan, kondisi, dan fasilitas.')

@push('styles')
    @vite('resources/css/peta-custom.css')
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO SECTION --}}
{{-- ============================================================ --}}
<section class="relative overflow-hidden text-white">
    <div class="absolute inset-0">
        <img src="{{ asset('images/fotosekolah.webp') }}"
             onerror="this.style.display='none';"
             alt=""
             class="w-full h-full object-cover scale-105 blur-[4px] brightness-50">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-900/80 via-primary-800/75 to-primary-700/70"></div>
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 md:pt-16 pb-24 md:pb-28 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs text-primary-100/90 mb-5 mt-4 animate-fade-in-up">
            <a href="{{ url('/') }}" class="hover:text-white transition-colors">Beranda</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <a href="{{ route('public.rooms.index') }}" class="hover:text-white transition-colors">Sarpras</a>
            <i data-lucide="chevron-right" class="w-3 h-3 opacity-60"></i>
            <span class="text-white font-medium">Peta Ruangan</span>
        </nav>

        <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight animate-fade-in-up stagger-2">
            Peta
            <span class="bg-gradient-to-r from-yellow-300 to-amber-200 bg-clip-text text-transparent">
                Ruangan
            </span>
        </h1>

        <p class="mt-4 max-w-2xl mx-auto text-sm md:text-base text-primary-100/90 animate-fade-in-up stagger-3">
            Klik titik pada denah untuk melihat detail ruangan — kondisi, kapasitas, dan fasilitasnya.
            Beralih ke mode 3D untuk eksplorasi gedung realistis.
        </p>
    </div>

    <x-public.wave />
</section>

{{-- ============================================================ --}}
{{-- MAIN CONTENT --}}
{{-- ============================================================ --}}
<section class="relative bg-surface-50 py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

    {{-- INFO BOX --}}
        <div class="relative bg-gradient-to-br from-primary-50 to-primary-100/50 rounded-2xl border border-primary-200/60 p-5 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-primary-200/30 blur-2xl pointer-events-none"></div>
            <div class="relative flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-600 flex items-center justify-center shrink-0">
                    <i data-lucide="info" class="w-5 h-5 text-white"></i>
                </div>
                <div class="text-xs sm:text-sm text-primary-900 leading-relaxed">
                    <p class="font-bold mb-2">Panduan Penggunaan Peta:</p>
                    <ul class="space-y-1 text-primary-800">
                        <li class="flex items-start gap-2">
                            <span class="text-primary-500 mt-0.5">•</span>
                            <span>Pilih tab <strong>Model 3D</strong> untuk tampilan denah 3 dimensi kampus SMK Negeri 2 Karanganyar.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-primary-500 mt-0.5">•</span>
                            <span>Klik langsung pada bangunan untuk membuka jendela informasi ruangan & data sarana prasarana.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-primary-500 mt-0.5">•</span>
                            <span>Gunakan pencarian untuk menyorot ruangan secara instan.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- VIEW TOGGLE — selalu tampil di atas --}}
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <div class="inline-flex bg-surface-100 p-1 rounded-xl">
                <button type="button" data-view="map"
                        class="view-toggle-btn px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 inline-flex items-center gap-1.5 bg-white text-primary-600 shadow-sm">
                    <i data-lucide="map" class="w-3.5 h-3.5"></i>
                    Peta 2D
                </button>
                <button type="button" data-view="3d"
                        class="view-toggle-btn px-4 py-2 rounded-lg text-xs font-semibold transition-all duration-200 inline-flex items-center gap-1.5 text-surface-500 hover:text-surface-700">
                    <i data-lucide="box" class="w-3.5 h-3.5"></i>
                    Model 3D
                </button>
            </div>

            {{-- CTA kecil ke menu daftar ruangan (opsional) --}}
            <a href="{{ route('public.rooms.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-primary-600 hover:bg-primary-50 transition-colors">
                <i data-lucide="list" class="w-3.5 h-3.5"></i>
                Daftar Ruangan Lengkap
                <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
        </div>

        {{-- FILTER & SEARCH — hanya di view 2D --}}
        <div id="search-filter-bar" class="bg-white rounded-2xl border border-surface-200 shadow-soft p-4 md:p-5 space-y-4">
            <div class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
                {{-- Search --}}
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="search"
                           id="room-search"
                           placeholder="Cari ruangan... (nama, kode, lokasi)"
                           class="w-full pl-10 pr-10 py-2.5 rounded-xl bg-surface-50 border border-surface-200 text-sm text-surface-800 placeholder-surface-400 focus:outline-none focus:ring-2 focus:ring-primary-500/30 focus:border-primary-400 transition-all duration-200">
                    <button type="button"
                            id="room-search-clear"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-surface-200 hover:bg-surface-300 text-surface-600 items-center justify-center hidden"
                            aria-label="Bersihkan pencarian">
                        <i data-lucide="x" class="w-3 h-3"></i>
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between pt-4 border-t border-surface-100">

                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider">Kondisi:</span>
                    @foreach (['' => 'Semua', 'baik' => 'Baik', 'rusak_ringan' => 'Rusak Ringan', 'rusak_berat' => 'Rusak Berat'] as $val => $label)
                        @php
                            $isActive = ($condition ?? '') === $val;
                            $dotColor = match($val) {
                                'baik'         => 'bg-emerald-500',
                                'rusak_ringan' => 'bg-amber-500',
                                'rusak_berat'  => 'bg-red-500',
                                default        => 'bg-surface-400',
                            };
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['condition' => $val ?: null]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $isActive ? 'bg-primary-600 text-white shadow-sm' : 'bg-surface-100 text-surface-600 hover:bg-surface-200' }}">
                            @if ($val)
                                <span class="w-2 h-2 rounded-full {{ $dotColor }} {{ $isActive ? 'ring-2 ring-white/40' : '' }}"></span>
                            @endif
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- VIEW: PETA 2D --}}
        {{-- ============================================================ --}}
        <div id="view-map" class="view-container">
            <div class="bg-white rounded-2xl border border-surface-200 shadow-lg p-4 sm:p-6">
                @if ($rooms->isEmpty())
                    <div class="text-center py-20">
                        <div class="w-20 h-20 rounded-full bg-surface-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="map-pin-off" class="w-10 h-10 text-surface-400"></i>
                        </div>
                        <h3 class="text-base font-semibold text-surface-700 mb-1">Belum ada data peta</h3>
                        <p class="text-sm text-surface-500 max-w-sm mx-auto">
                            Admin belum menentukan koordinat ruangan di denah, atau filter yang dipilih tidak cocok.
                        </p>
                    </div>
                @else
                    <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                        <div class="flex items-center gap-2 text-xs text-surface-500">
                            <i data-lucide="mouse-pointer-2" class="w-3.5 h-3.5"></i>
                            <span>Drag untuk jelajah, scroll/pinch untuk zoom</span>
                        </div>

                        <div class="flex items-center gap-1 bg-surface-100 p-1 rounded-lg">
                            <button type="button" id="zoom-out" class="w-8 h-8 rounded-md hover:bg-white flex items-center justify-center text-surface-600 transition-colors" aria-label="Zoom out">
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <button type="button" id="zoom-reset" class="px-3 h-8 rounded-md hover:bg-white flex items-center justify-center text-xs font-semibold text-surface-600 transition-colors">
                                Reset
                            </button>
                            <button type="button" id="zoom-in" class="w-8 h-8 rounded-md hover:bg-white flex items-center justify-center text-surface-600 transition-colors" aria-label="Zoom in">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <div id="map-viewport" class="relative w-full overflow-hidden rounded-xl bg-surface-50 border border-surface-100" style="aspect-ratio: 4 / 3; cursor: grab;">
                        <div id="map-content" class="absolute inset-0 origin-top-left">
                            <img src="{{ asset('images/denah-sekolah.jpeg') }}"
                                 alt="Denah Sekolah"
                                 class="absolute inset-0 w-full h-full object-contain pointer-events-none select-none">

                            @foreach ($rooms as $room)
                                @php
                                    $colorHex = match($room->condition->value) {
                                        'baik'         => '#10b981',
                                        'rusak_ringan' => '#f59e0b',
                                        'rusak_berat'  => '#ef4444',
                                        default        => '#94a3b8',
                                    };
                                @endphp

                                <button type="button"
                                        class="room-marker group absolute -translate-x-1/2 -translate-y-1/2 z-10 p-2 -m-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:rounded-full"
                                        style="left: {{ $room->map_x }}%; top: {{ $room->map_y }}%;"
                                        data-room-id="{{ $room->id }}"
                                        data-room-code="{{ $room->code }}"
                                        data-room-name="{{ $room->name }}"
                                        data-room-condition="{{ $room->condition->value }}"
                                        data-room-condition-label="{{ $room->condition->label() }}"
                                        data-room-floor="{{ $room->map_floor ?? $room->floor }}"
                                        data-room-capacity="{{ $room->capacity }}"
                                        data-room-health="{{ $room->health_score }}"
                                        data-room-photo="{{ $room->photo ? asset('storage/' . $room->photo) : '' }}"
                                        data-room-url="{{ route('public.rooms.show', $room->code) }}"
                                        data-room-report-url="{{ route('public.sarpras.pengaduan', ['room_id' => $room->id]) }}"
                                        aria-label="Ruangan {{ $room->name }} — {{ $room->condition->label() }}"
                                        title="{{ $room->name }}">

                                    <span class="absolute inline-flex h-full w-full rounded-full opacity-60 animate-ping" style="background-color: {{ $colorHex }};"></span>
                                    <span class="relative flex items-center justify-center w-6 h-6 md:w-7 md:h-7 rounded-full ring-2 ring-white shadow-lg transition-all duration-300 group-hover:scale-125 group-hover:ring-4 group-hover:ring-primary-300/40" style="background-color: {{ $colorHex }};">
                                        <i data-lucide="building" class="w-3 h-3 text-white"></i>
                                    </span>

                                    <div class="pointer-events-none absolute left-1/2 -translate-x-1/2 bottom-full mb-3 opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-all duration-200 translate-y-1 group-hover:translate-y-0 whitespace-nowrap z-20">
                                        <div class="bg-surface-900 text-white text-xs rounded-lg px-3 py-2 shadow-xl">
                                            <p class="font-semibold">{{ $room->name }}</p>
                                            <p class="text-[10px] text-surface-300 mt-0.5">{{ $room->code }} · {{ $room->condition->label() }}</p>
                                        </div>
                                        <div class="w-2 h-2 bg-surface-900 rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-1"></div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div id="no-result-message" class="hidden text-center py-12">
                        <i data-lucide="search-x" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                        <p class="text-sm text-surface-500">Tidak ada ruangan yang cocok dengan pencarian.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- VIEW: 3D MODEL --}}
        {{-- ============================================================ --}}
        <div id="view-3d" class="view-container hidden space-y-4">

            {{-- Card: Pencari Rute (DI LUAR container 3D) --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-lg p-4 sm:p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-cyan-600 flex items-center justify-center">
                        <i data-lucide="route" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-surface-900">Pencari Rute</h3>
                        <p class="text-[11px] text-surface-500">Cari jalur dari satu ruangan ke ruangan lain</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-[1fr_1fr_auto] gap-2 sm:gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-surface-500 uppercase tracking-wider mb-1">Dari</label>
                        <select id="route-from" class="w-full px-3 py-2.5 text-sm rounded-lg border border-surface-300 bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition">
                            <option value="">— Pilih ruangan awal —</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-surface-500 uppercase tracking-wider mb-1">Ke</label>
                        <select id="route-to" class="w-full px-3 py-2.5 text-sm rounded-lg border border-surface-300 bg-white focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition">
                            <option value="">— Pilih ruangan tujuan —</option>
                        </select>
                    </div>
                    <div class="flex gap-2 items-end">
                        <button type="button" id="route-go"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold transition shadow-sm">
                            <i data-lucide="navigation" class="w-4 h-4"></i>
                            <span class="sm:hidden lg:inline">Cari</span>
                            <span class="hidden sm:inline lg:hidden">Cari Rute</span>
                        </button>
                        <button type="button" id="route-clear"
                                class="px-4 py-2.5 rounded-lg bg-surface-100 hover:bg-surface-200 text-surface-700 text-sm font-semibold transition">
                            Clear
                        </button>
                    </div>
                </div>

                <div id="route-panel" class="hidden mt-3 p-3 bg-cyan-50/50 rounded-lg border border-cyan-200">
                    <div id="route-steps"></div>
                </div>
            </div>

            {{-- Card: Container 3D (dengan overlay Daftar Ruangan) --}}
            <div class="bg-white rounded-2xl border border-surface-200 shadow-lg p-4 sm:p-5">
                <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
                    <div class="flex items-center gap-2 text-xs text-surface-500">
                        <i data-lucide="orbit" class="w-3.5 h-3.5 text-primary-600"></i>
                        <span>Model 3D Interaktif</span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" id="btn-toggle-labels" class="px-3 h-8 rounded-lg bg-surface-100 hover:bg-surface-200 text-xs font-semibold text-surface-700 transition inline-flex items-center gap-1.5">
                            <i data-lucide="tag" class="w-3.5 h-3.5"></i>
                            Sembunyikan Label
                        </button>
                        <button type="button" id="btn-3d-rotate" class="px-3 h-8 rounded-lg bg-surface-100 hover:bg-surface-200 text-xs font-semibold text-surface-700 transition">
                            Putar otomatis: mati
                        </button>
                        <button type="button" id="btn-3d-reset" class="px-3 h-8 rounded-lg bg-surface-100 hover:bg-surface-200 text-xs font-semibold text-surface-700 transition">
                            Reset Posisi
                        </button>
                    </div>
                </div>

                <div id="three-container" class="relative w-full rounded-xl overflow-hidden bg-slate-900 border border-surface-200" style="height: 640px;">

                    {{-- OVERLAY: Tombol Daftar Ruangan (kanan atas) --}}
                    <div class="absolute top-3 right-3 z-10">
                        <button type="button" id="rooms-list-toggle"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white/95 backdrop-blur-md border border-surface-200 shadow-lg text-xs font-semibold text-surface-800 hover:bg-white transition">
                            <i data-lucide="list" class="w-3.5 h-3.5 text-primary-600"></i>
                            Daftar Ruangan
                        </button>
                    </div>

                    {{-- OVERLAY: Panel Daftar Ruangan (kanan atas, muncul saat tombol diklik) --}}
                    <div id="rooms-list-panel" class="hidden absolute top-16 right-3 z-20 w-[280px] max-h-[calc(100%-80px)] bg-white/98 backdrop-blur-md rounded-xl border border-surface-200 shadow-2xl overflow-hidden flex flex-col">

                        {{-- Header --}}
                        <div class="flex items-center justify-between px-3 py-2 border-b border-surface-200 bg-gradient-to-r from-primary-50 to-white shrink-0">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="building-2" class="w-3.5 h-3.5 text-primary-600"></i>
                                <span class="text-xs font-bold text-surface-800">Daftar Ruangan</span>
                            </div>
                            <button type="button" id="rooms-list-close" class="w-6 h-6 rounded-md hover:bg-surface-100 flex items-center justify-center text-surface-500 transition">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        {{-- Search --}}
                        <div class="px-3 py-2 border-b border-surface-100 shrink-0">
                            <input type="text" id="rooms-list-search" placeholder="Cari ruangan..."
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-surface-200 bg-surface-50 focus:bg-white focus:border-primary-400 focus:ring-2 focus:ring-primary-500/20 outline-none transition">
                        </div>

                        {{-- List --}}
                        <div id="rooms-list-items" class="flex-1 overflow-y-auto p-2 space-y-1"></div>
                    </div>

                    {{-- Badge label count --}}
                    <div class="absolute bottom-3 left-3 pointer-events-none z-10">
                        <div class="bg-white/85 backdrop-blur-md px-2.5 py-1 rounded-full border border-surface-200 text-[10px] text-surface-700 font-medium shadow-sm">
                            <span id="tooltip-count">0</span> label aktif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- VIEW: LIST --}}
        {{-- ============================================================ --}}
        <div id="view-list" class="view-container hidden">
            <div id="list-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($rooms as $room)
                    @php
                        $conditionColor = match($room->condition->value) {
                            'baik'         => 'emerald',
                            'rusak_ringan' => 'amber',
                            'rusak_berat'  => 'red',
                            default        => 'surface',
                        };
                    @endphp

                    <button type="button"
                            class="room-card group text-left bg-white rounded-2xl border border-surface-200 overflow-hidden hover:-translate-y-1 hover:shadow-lg hover:border-primary-200 transition-all duration-300"
                            data-room-id="{{ $room->id }}"
                            data-room-code="{{ $room->code }}"
                            data-room-name="{{ $room->name }}"
                            data-room-condition="{{ $room->condition->value }}"
                            data-room-condition-label="{{ $room->condition->label() }}"
                            data-room-floor="{{ $room->map_floor ?? $room->floor }}"
                            data-room-capacity="{{ $room->capacity }}"
                            data-room-health="{{ $room->health_score }}"
                            data-room-photo="{{ $room->photo ? asset('storage/' . $room->photo) : '' }}"
                            data-room-url="{{ route('public.rooms.show', $room->code) }}"
                            data-room-report-url="{{ route('public.sarpras.pengaduan', ['room_id' => $room->id]) }}">

                        <div class="relative h-40 bg-surface-100 overflow-hidden">
                            @if ($room->photo)
                                <img src="{{ asset('storage/' . $room->photo) }}"
                                     alt="{{ $room->name }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                                    <i data-lucide="door-open" class="w-12 h-12 text-primary-400"></i>
                                </div>
                            @endif

                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-{{ $conditionColor }}-500 text-white text-[10px] font-bold uppercase tracking-wide shadow-sm">
                                {{ $room->condition->label() }}
                            </span>

                            @if ($room->map_floor || $room->floor)
                                <span class="absolute top-3 right-3 px-2 py-1 rounded-full bg-white/95 backdrop-blur-sm text-[10px] font-bold text-surface-700 shadow-sm">
                                    Lt. {{ $room->map_floor ?? $room->floor }}
                                </span>
                            @endif
                        </div>

                        <div class="p-4">
                            <p class="text-[10px] font-mono text-surface-400 tracking-wider">{{ $room->code }}</p>
                            <h3 class="mt-1 text-sm font-bold text-surface-900 leading-tight line-clamp-2 group-hover:text-primary-700 transition-colors">
                                {{ $room->name }}
                            </h3>

                            <div class="mt-3 flex items-center gap-3 text-[11px] text-surface-500">
                                @if ($room->capacity)
                                    <span class="inline-flex items-center gap-1">
                                        <i data-lucide="users" class="w-3 h-3"></i>
                                        {{ $room->capacity }}
                                    </span>
                                @endif
                            </div>

                            <div class="mt-3 pt-3 border-t border-surface-100 flex items-center justify-between">
                                <span class="text-[10px] text-surface-400">Klik untuk detail</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-primary-600 transition-transform group-hover:translate-x-1"></i>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>

            <div id="list-empty" class="hidden text-center py-20">
                <i data-lucide="search-x" class="w-12 h-12 text-surface-300 mx-auto mb-3"></i>
                <p class="text-sm text-surface-500">Tidak ada ruangan yang cocok dengan pencarian.</p>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- MODAL DETAIL RUANGAN --}}
{{-- ============================================================ --}}
<div id="room-modal"
     class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="room-modal-title">

    <div id="room-modal-backdrop" class="absolute inset-0 bg-surface-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>

    <div id="room-modal-content"
         class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl scale-95 opacity-0 transition-all duration-300 max-h-[90vh] overflow-hidden flex flex-col">

        <div class="relative shrink-0">
            <div class="relative h-40 bg-surface-100 overflow-hidden">
                <img id="room-modal-photo" src="" alt="" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-surface-900/70 via-surface-900/20 to-transparent"></div>

                <button type="button" id="room-modal-close"
                        class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/95 backdrop-blur-sm flex items-center justify-center hover:bg-white transition-colors shadow-lg"
                        aria-label="Tutup">
                    <i data-lucide="x" class="w-4 h-4 text-surface-700"></i>
                </button>

                <div class="absolute top-3 left-3">
                    <span id="room-modal-condition" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-lg">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        <span class="text-white">Kondisi</span>
                    </span>
                </div>

                <div class="absolute bottom-4 left-5 right-5">
                    <p id="room-modal-code" class="text-xs font-mono text-white/80 tracking-wider"></p>
                    <h3 id="room-modal-title" class="text-xl font-bold text-white leading-tight mt-0.5"></h3>
                </div>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-4">
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-surface-50 rounded-xl p-3 text-center">
                    <i data-lucide="users" class="w-4 h-4 text-primary-600 mx-auto mb-1"></i>
                    <p class="text-[10px] text-surface-500">Kapasitas</p>
                    <p id="room-modal-capacity" class="text-sm font-bold text-surface-900">—</p>
                </div>
                <div class="bg-surface-50 rounded-xl p-3 text-center">
                    <i data-lucide="activity" class="w-4 h-4 text-primary-600 mx-auto mb-1"></i>
                    <p class="text-[10px] text-surface-500">Skor</p>
                    <p id="room-modal-health" class="text-sm font-bold text-surface-900">—</p>
                </div>
            </div>

            <div id="room-modal-location-wrapper" class="hidden">
                <p class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-1">Lokasi</p>
                <p id="room-modal-location" class="text-sm text-surface-700 flex items-start gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-primary-600 shrink-0 mt-0.5"></i>
                    <span></span>
                </p>
            </div>

            <div class="space-y-2 pt-2">
                <a id="room-modal-detail-link" href="#"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors group">
                    <i data-lucide="door-open" class="w-4 h-4"></i>
                    Lihat Detail Lengkap
                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                </a>
                <a id="room-modal-report-link" href="#"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white border-2 border-surface-200 hover:border-red-300 hover:bg-red-50 text-surface-700 hover:text-red-700 text-sm font-semibold transition-colors">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Laporkan Kerusakan
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        window.serverRoomsData = @json($roomsForJs ?? $rooms);
    </script>
    @vite(['resources/js/vendor-3d.js', 'resources/js/peta-handler.js', 'resources/js/route-planner.js'])
@endpush