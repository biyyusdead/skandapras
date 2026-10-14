@extends('layouts.backend')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    @php
        $myRooms = \App\Models\Room::whereHas('roomUsers', fn($q) => $q->where('user_id', auth()->id()))
            ->withCount('items')
            ->get();
        $myReports = \App\Models\DamageReport::where('reporter_id', auth()->id())->latest()->take(5)->get();
        $pendingCount = \App\Models\DamageReport::where('reporter_id', auth()->id())->pending()->count();
        $totalItems = $myRooms->sum('items_count');
    @endphp

    <x-ui.page-header title="Dashboard" subtitle="Ringkasan ruangan dan laporan Anda" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Ruangan Saya" :value="$myRooms->count()" icon="door-open" color="primary" />
        <x-ui.stat-card label="Total Barang" :value="$totalItems" icon="package" color="indigo" />
        <x-ui.stat-card label="Laporan Saya" :value="\App\Models\DamageReport::where('reporter_id', auth()->id())->count()" icon="file-text" color="yellow" />
        <x-ui.stat-card label="Menunggu Proses" :value="$pendingCount" icon="clock" color="red" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Ruangan Saya --}}
        <x-ui.card title="Ruangan Saya" subtitle="Ruangan yang Anda tanggung jawab">
            @if ($myRooms->isEmpty())
                <x-ui.empty-state icon="door-open" title="Belum ada ruangan"
                    description="Hubungi Waka Sarpras untuk penugasan ruangan." />
            @else
                <ul class="divide-y divide-surface-100 -mx-5 -mb-5">
                    @foreach ($myRooms as $room)
                        <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
                            <a href="{{ route('pengguna.rooms.show', $room) }}" class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-surface-800 truncate">{{ $room->name }}</p>
                                    <p class="text-xs text-surface-500">
                                        <span class="font-mono">{{ $room->code }}</span> · {{ $room->items_count }} barang
                                    </p>
                                </div>
                                <x-ui.badge :color="$room->condition->color()" :dot="true">{{ $room->condition->label() }}</x-ui.badge>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        {{-- Laporan Terbaru --}}
        <x-ui.card title="Laporan Terbaru Saya">
            @if ($myReports->isEmpty())
                <x-ui.empty-state icon="file-text" title="Belum ada laporan"
                    description="Buat laporan jika menemukan kerusakan.">
                    <x-slot:actions>
                        <x-ui.button href="{{ route('pengguna.damage-reports.create') }}" icon="plus" size="sm">
                            Buat Laporan
                        </x-ui.button>
                    </x-slot:actions>
                </x-ui.empty-state>
            @else
                <ul class="divide-y divide-surface-100 -mx-5 -mb-5">
                    @foreach ($myReports as $report)
                        <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
                            <a href="{{ route('pengguna.damage-reports.show', $report) }}" class="block">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-mono text-surface-500">{{ $report->report_code }}</p>
                                        <p class="text-sm text-surface-800 truncate mt-0.5">
                                            {{ $report->item?->name ?? $report->room->name }}
                                        </p>
                                        <p class="text-xs text-surface-400 mt-0.5">{{ $report->reported_at?->diffForHumans() }}</p>
                                    </div>
                                    <x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

    </div>

@endsection