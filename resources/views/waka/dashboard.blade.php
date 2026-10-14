@extends('layouts.backend')

@section('title', 'Dashboard Sarpras')
@section('page-title', 'Dashboard Sarpras')

@section('content')

    @php
        use App\Enums\DamageReportStatus;
        $totalRooms = \App\Models\Room::count();
        $totalItems = \App\Models\Item::count();
        $pending    = \App\Models\DamageReport::whereIn('status', ['menunggu_verifikasi', 'diverifikasi', 'menunggu_persetujuan_1'])->count();
        $waitingMe  = \App\Models\DamageReport::whereIn('status', ['menunggu_verifikasi', 'diverifikasi'])->count();
        $processing = \App\Models\DamageReport::whereIn('status', ['disetujui', 'siap_diperbaiki', 'diproses'])->count();
        $done       = \App\Models\DamageReport::where('status', 'selesai')->count();
    @endphp

    <x-ui.page-header title="Dashboard Sarpras" subtitle="Ringkasan kondisi sarana dan prasarana" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Ruangan" :value="$totalRooms" icon="door-open" color="primary" />
        <x-ui.stat-card label="Total Barang" :value="$totalItems" icon="package" color="indigo" />
        <x-ui.stat-card label="Menunggu Tindakan" :value="$waitingMe" icon="alert-triangle" color="yellow" />
        <x-ui.stat-card label="Dalam Proses" :value="$processing" icon="loader" color="blue" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Kondisi Ruangan --}}
        <x-ui.card title="Kondisi Ruangan" class="lg:col-span-1">
            @php
                $good = \App\Models\Room::where('condition', 'baik')->count();
                $minor = \App\Models\Room::where('condition', 'rusak_ringan')->count();
                $severe = \App\Models\Room::where('condition', 'rusak_berat')->count();
            @endphp
            @if ($totalRooms > 0)
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span class="text-surface-600">Baik</span><span class="font-semibold text-green-600">{{ $good }}</span></div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden"><div class="h-full bg-green-500 rounded-full" style="width: {{ ($good/$totalRooms)*100 }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span class="text-surface-600">Rusak Ringan</span><span class="font-semibold text-yellow-600">{{ $minor }}</span></div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden"><div class="h-full bg-yellow-500 rounded-full" style="width: {{ ($minor/$totalRooms)*100 }}%"></div></div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1"><span class="text-surface-600">Rusak Berat</span><span class="font-semibold text-red-600">{{ $severe }}</span></div>
                        <div class="w-full h-2 bg-surface-100 rounded-full overflow-hidden"><div class="h-full bg-red-500 rounded-full" style="width: {{ ($severe/$totalRooms)*100 }}%"></div></div>
                    </div>
                </div>
            @else
                <x-ui.empty-state icon="door-open" title="Belum ada data" />
            @endif
        </x-ui.card>

        {{-- Laporan Terbaru --}}
        <x-ui.card title="Laporan Terbaru" class="lg:col-span-2">
            @php $recent = \App\Models\DamageReport::with(['room:id,name', 'reporter:id,name'])->latest()->take(5)->get(); @endphp
            @if ($recent->isEmpty())
                <x-ui.empty-state icon="file-text" title="Belum ada laporan" />
            @else
                <ul class="divide-y divide-surface-100 -mx-5 -mb-5">
                    @foreach ($recent as $report)
                        <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
                            <a href="{{ route('waka.damage-reports.show', $report) }}" class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-mono text-surface-500">{{ $report->report_code }}</p>
                                    <p class="text-sm text-surface-800 truncate mt-0.5">{{ $report->room->name }}</p>
                                    <p class="text-xs text-surface-400 mt-0.5">{{ $report->reporter?->name ?? 'Pengunjung' }} · {{ $report->reported_at?->diffForHumans() }}</p>
                                </div>
                                <x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>

    {{-- CTA Approvals --}}
    @if ($waitingMe > 0)
        <x-ui.card class="border-yellow-200/60 bg-yellow-50/40">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center shrink-0">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-surface-900">{{ $waitingMe }} laporan menunggu tindakan Anda</p>
                    <p class="text-xs text-surface-500 mt-0.5">Segera verifikasi atau setujui laporan tersebut.</p>
                </div>
                <x-ui.button href="{{ route('waka.approvals.index') }}" icon="arrow-right">Tangani Sekarang</x-ui.button>
            </div>
        </x-ui.card>
    @endif

@endsection