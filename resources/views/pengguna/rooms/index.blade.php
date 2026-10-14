@extends('layouts.backend')

@section('title', 'Ruangan Saya')
@section('page-title', 'Ruangan Saya')

@section('content')

    <x-ui.page-header title="Ruangan Saya" subtitle="Ruangan yang menjadi tanggung jawab Anda" />

    @if ($rooms->isEmpty())
        <x-ui.card>
            <x-ui.empty-state icon="door-open" title="Belum ada ruangan"
                description="Hubungi Waka Sarpras untuk penugasan ruangan." />
        </x-ui.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($rooms as $room)
                <a href="{{ route('pengguna.rooms.show', $room) }}"
                   class="group bg-white border border-surface-200/70 rounded-card shadow-card overflow-hidden hover-lift block">
                    <div class="relative aspect-video bg-surface-100 overflow-hidden">
                        @if ($room->photo)
                            <img src="{{ asset('storage/' . $room->photo) }}" alt="{{ $room->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i data-lucide="door-open" class="w-10 h-10 text-surface-300"></i>
                            </div>
                        @endif

                        @if ($room->panorama_url)
                            <div class="absolute top-3 right-3 px-2 py-1 rounded-full bg-primary-600/90 text-white text-[10px] font-medium
                                        backdrop-blur-sm flex items-center gap-1">
                                <i data-lucide="rotate-3d" class="w-3 h-3"></i> 360°
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-surface-900 truncate">{{ $room->name }}</h3>
                        <p class="text-xs text-surface-500 mt-0.5">
                            <span class="font-mono">{{ $room->code }}</span>
                            @if ($room->floor) · Lantai {{ $room->floor }} @endif
                        </p>
                        <div class="flex items-center gap-1.5 flex-wrap mt-3">
                            <x-ui.badge :color="$room->condition->color()">{{ $room->condition->label() }}</x-ui.badge>
                            <x-ui.badge color="gray" icon="package">{{ $room->items_count }} barang</x-ui.badge>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

@endsection