@extends('layouts.backend')

@section('title', $room->name)
@section('page-title', 'Monitoring Ruangan')

@section('content')

    <x-ui.page-header :title="$room->name" :subtitle="'Kode: ' . $room->code">
        <x-slot:actions>
            <x-ui.button href="{{ route('kepala.rooms.index') }}" variant="secondary" icon="arrow-left">Kembali</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Virtual Tour / Foto Hero --}}
    @if ($room->panorama_url || $room->photo)
        <div class="mb-5">
            <x-ui.card :padding="false" class="overflow-hidden">
                <div class="relative aspect-video bg-surface-900">
                    @if ($room->panorama_url)
                        <iframe src="{{ $room->panorama_url }}"
                                class="absolute inset-0 w-full h-full"
                                frameborder="0" allowfullscreen
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        <div class="absolute top-3 left-3 px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-sm text-white text-xs font-medium
                                    flex items-center gap-1.5">
                            <i data-lucide="rotate-3d" class="w-3.5 h-3.5"></i> Virtual Tour 360°
                        </div>
                    @else
                        <img src="{{ asset('storage/' . $room->photo) }}" alt="{{ $room->name }}"
                             class="absolute inset-0 w-full h-full object-cover">
                    @endif
                </div>
            </x-ui.card>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI --}}
        <div class="lg:col-span-2 space-y-5">

            <x-ui.card title="Informasi Ruangan">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Nama Ruangan</dt>
                        <dd class="text-sm text-surface-800 font-medium">{{ $room->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Kode</dt>
                        <dd class="text-sm font-mono text-surface-800">{{ $room->code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Lokasi</dt>
                        <dd class="text-sm text-surface-800">{{ $room->location ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Lantai</dt>
                        <dd class="text-sm text-surface-800">{{ $room->floor ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Kapasitas</dt>
                        <dd class="text-sm text-surface-800">{{ $room->capacity ? $room->capacity . ' orang' : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Kondisi</dt>
                        <dd><x-ui.badge :color="$room->condition->color()">{{ $room->condition->label() }}</x-ui.badge></dd>
                    </div>
                    @if ($room->description)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Deskripsi</dt>
                            <dd class="text-sm text-surface-700 leading-relaxed">{{ $room->description }}</dd>
                        </div>
                    @endif
                </dl>
            </x-ui.card>

            {{-- Daftar Barang --}}
            <x-ui.card :padding="false" :title="'Daftar Barang (' . $room->itemRooms->count() . ' jenis)'">
                @if ($room->itemRooms->isEmpty())
                    <div class="p-5"><x-ui.empty-state icon="package" title="Belum ada barang" /></div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                                    <th class="px-4 py-3 font-medium w-12">No</th>
                                    <th class="px-4 py-3 font-medium">Nama Barang</th>
                                    <th class="px-4 py-3 font-medium">Kategori</th>
                                    <th class="px-4 py-3 font-medium w-20">Jumlah</th>
                                    <th class="px-4 py-3 font-medium w-28">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-100">
                                @foreach ($room->itemRooms as $i => $ir)
                                    <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                        <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-surface-800">{{ $ir->item->name }}</p>
                                            @if ($ir->item->brand) <p class="text-xs text-surface-400">{{ $ir->item->brand }}</p> @endif
                                        </td>
                                        <td class="px-4 py-3"><x-ui.badge color="purple">{{ $ir->item->category->name }}</x-ui.badge></td>
                                        <td class="px-4 py-3 text-surface-700 tabular-nums">{{ $ir->quantity }}</td>
                                        <td class="px-4 py-3"><x-ui.badge :color="$ir->condition->color()">{{ $ir->condition->label() }}</x-ui.badge></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>

            {{-- Riwayat Laporan --}}
            <x-ui.card :padding="false" title="Riwayat Laporan Kerusakan">
                @if ($room->damageReports->isEmpty())
                    <div class="p-5"><x-ui.empty-state icon="alert-triangle" title="Belum ada laporan" /></div>
                @else
                    <ul class="divide-y divide-surface-100">
                        @foreach ($room->damageReports as $report)
                            <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
                                <a href="{{ route('kepala.damage-reports.show', $report) }}" class="block">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-xs font-mono text-surface-500">{{ $report->report_code }}</span>
                                                <x-ui.badge :color="$report->status->color()" :dot="true">{{ $report->status->label() }}</x-ui.badge>
                                            </div>
                                            <p class="text-sm text-surface-700 line-clamp-2">{{ $report->description }}</p>
                                            <p class="text-xs text-surface-400 mt-1">
                                                {{ $report->reporter?->name ?? 'Pengunjung' }} · {{ $report->reported_at?->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

        </div>

        {{-- KANAN --}}
        <div class="lg:col-span-1 space-y-5">

            <x-ui.card title="Kondisi Ruangan">
                @php
                    $score = $room->health_score;
                    $scoreColor = $score >= 70 ? 'green' : ($score >= 40 ? 'yellow' : 'red');
                    $scoreLabel = $score >= 70 ? 'Baik' : ($score >= 40 ? 'Cukup' : 'Buruk');
                @endphp
                <div class="text-center py-4">
                    <div class="relative inline-flex items-center justify-center w-32 h-32 mb-3">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none"
                                    class="text-surface-100" stroke="currentColor" stroke-width="2"></circle>
                            <circle cx="18" cy="18" r="16" fill="none"
                                    class="text-{{ $scoreColor }}-500" stroke="currentColor" stroke-width="2"
                                    stroke-dasharray="{{ $score * 1.005 }} 100" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-bold text-surface-900 tabular-nums">{{ $score }}%</span>
                        </div>
                    </div>
                    <p class="text-sm text-surface-500">Health Score</p>
                    <div class="mt-2">
                        <x-ui.badge :color="$scoreColor" :dot="true">{{ $scoreLabel }}</x-ui.badge>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Penanggung Jawab">
                @if ($room->roomUsers->isEmpty())
                    <p class="text-sm text-surface-500">Belum ada penanggung jawab.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($room->roomUsers as $ru)
                            <li class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-400 to-primary-600
                                            text-white flex items-center justify-center text-xs font-semibold shrink-0">
                                    {{ strtoupper(substr($ru->user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-surface-800 truncate">{{ $ru->user->name }}</p>
                                    <p class="text-xs text-surface-400">{{ $ru->assigned_at?->format('d M Y') ?? '—' }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            <x-ui.card title="Statistik">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-500">Total Barang</span>
                        <span class="text-sm font-semibold text-surface-800 tabular-nums">{{ $room->items->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-500">Barang Rusak</span>
                        <span class="text-sm font-semibold text-surface-800 tabular-nums">
                            {{ $room->items->whereIn('condition', ['rusak_ringan', 'rusak_berat'])->count() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-surface-500">Total Laporan</span>
                        <span class="text-sm font-semibold text-surface-800 tabular-nums">{{ $room->damageReports->count() }}</span>
                    </div>
                </div>
            </x-ui.card>

        </div>
    </div>

@endsection