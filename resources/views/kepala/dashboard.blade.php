@extends('layouts.backend')

@section('title', 'Dashboard Kepala Sekolah')
@section('page-title', 'Dashboard Kepala Sekolah')

@section('content')

    @php
        $totalRooms   = \App\Models\Room::count();
        $avgScore     = round(\App\Models\Room::avg('health_score') ?? 0);
        $goodRooms    = \App\Models\Room::where('condition', 'baik')->count();
        $badRooms     = \App\Models\Room::whereIn('condition', ['rusak_ringan', 'rusak_berat'])->count();
        $totalReports = \App\Models\DamageReport::count();
        $pendingApprovals = \App\Models\DamageReport::where('status', 'menunggu_persetujuan_2')->count();
    @endphp

    <x-ui.page-header title="Dashboard Kepala Sekolah"
        subtitle="Ringkasan monitoring sarana dan prasarana sekolah" />

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Ruangan" :value="$totalRooms" icon="door-open" color="primary" />
        <x-ui.stat-card label="Rata-rata Skor" :value="$avgScore . '%'" icon="gauge" color="green" />
        <x-ui.stat-card label="Ruangan Bermasalah" :value="$badRooms" icon="alert-triangle" color="red" />
        <x-ui.stat-card label="Menunggu Persetujuan" :value="$pendingApprovals" icon="clock" color="yellow" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Kondisi Ruangan --}}
        <x-ui.card title="Kondisi Ruangan">
            @if ($totalRooms > 0)
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-surface-600">Baik</span>
                            <span class="font-semibold text-green-600">{{ $goodRooms }} ruangan</span>
                        </div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ ($goodRooms/$totalRooms)*100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-surface-600">Rusak Ringan</span>
                            <span class="font-semibold text-yellow-600">{{ \App\Models\Room::where('condition', 'rusak_ringan')->count() }} ruangan</span>
                        </div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-500 rounded-full" style="width: {{ (\App\Models\Room::where('condition', 'rusak_ringan')->count()/$totalRooms)*100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1.5">
                            <span class="text-surface-600">Rusak Berat</span>
                            <span class="font-semibold text-red-600">{{ \App\Models\Room::where('condition', 'rusak_berat')->count() }} ruangan</span>
                        </div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden">
                            <div class="h-full bg-red-500 rounded-full" style="width: {{ (\App\Models\Room::where('condition', 'rusak_berat')->count()/$totalRooms)*100 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <x-ui.empty-state icon="door-open" title="Belum ada data ruangan" />
            @endif
        </x-ui.card>

        {{-- Approval Pending --}}
        <x-ui.card title="Persetujuan Tahap 2 Menunggu">
            @php
                $pendingList = \App\Models\DamageReport::with(['room:id,name', 'reporter:id,name'])
                    ->where('status', 'menunggu_persetujuan_2')
                    ->latest()->take(5)->get();
            @endphp
            @if ($pendingList->isEmpty())
                <x-ui.empty-state icon="check-circle" title="Tidak ada yang menunggu persetujuan" />
            @else
                <ul class="divide-y divide-surface-100 -mx-5 -mb-5">
                    @foreach ($pendingList as $report)
                        <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
                            <a href="{{ route('kepala.damage-reports.show', $report) }}" class="block">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-mono text-surface-500">{{ $report->report_code }}</p>
                                        <p class="text-sm text-surface-800 truncate mt-0.5">{{ $report->room->name }}</p>
                                        <p class="text-xs text-surface-400 mt-0.5">{{ $report->reporter?->name ?? 'Pengunjung' }}</p>
                                    </div>
                                    <x-ui.badge color="yellow" :dot="true">Menunggu</x-ui.badge>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

    </div>

    {{-- Ruangan Bermasalah --}}
    <x-ui.card :padding="false" title="Ruangan dengan Skor Terendah"
        subtitle="Ruangan yang membutuhkan perhatian">
        @php
            $worstRooms = \App\Models\Room::orderBy('health_score')->take(5)->get();
        @endphp
        @if ($worstRooms->isEmpty())
            <div class="p-5"><x-ui.empty-state icon="door-open" title="Belum ada data" /></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Ruangan</th>
                            <th class="px-4 py-3 font-medium">Kondisi</th>
                            <th class="px-4 py-3 font-medium">Health Score</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($worstRooms as $i => $room)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $i + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-800">{{ $room->name }}</p>
                                    <p class="text-xs text-surface-400 font-mono">{{ $room->code }}</p>
                                </td>
                                <td class="px-4 py-3"><x-ui.badge :color="$room->condition->color()">{{ $room->condition->label() }}</x-ui.badge></td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 max-w-32 h-1.5 bg-surface-100 rounded-full overflow-hidden">
                                            @php $sc = $room->health_score >= 70 ? 'bg-green-500' : ($room->health_score >= 40 ? 'bg-yellow-500' : 'bg-red-500'); @endphp
                                            <div class="h-full {{ $sc }} rounded-full" style="width: {{ $room->health_score }}%"></div>
                                        </div>
                                        <span class="text-xs font-medium text-surface-700 tabular-nums">{{ $room->health_score }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.table-action :href="route('kepala.rooms.show', $room)" icon="eye" title="Detail" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>

@endsection