@extends('layouts.backend')

@section('title', $room->name)
@section('page-title', 'Detail Ruangan')

@section('content')

    <x-ui.page-header :title="$room->name" :subtitle="'Kode: ' . $room->code">
        <x-slot:actions>
            <x-ui.button href="{{ route('pengguna.rooms.index') }}" variant="secondary" icon="arrow-left">Kembali</x-ui.button>
            <x-ui.button href="{{ route('pengguna.damage-reports.create', ['room_id' => $room->id]) }}"
                         variant="danger" icon="alert-triangle">
                Buat Laporan Kerusakan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Virtual Tour / Foto Hero --}}
    @if ($room->panorama_url || $room->photo)
        <div class="mb-5">
            <x-ui.card :padding="false" class="overflow-hidden">
                <div class="relative aspect-video bg-surface-900">
                    @if ($room->panorama_url)
                        <iframe src="{{ $room->panorama_url }}" class="absolute inset-0 w-full h-full"
                                frameborder="0" allowfullscreen></iframe>
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
        <div class="lg:col-span-2 space-y-5">
            {{-- Info --}}
            <x-ui.card title="Informasi Ruangan">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Nama</dt>
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
                </dl>
            </x-ui.card>

            {{-- Daftar Barang --}}
            <x-ui.card :padding="false" :title="'Daftar Barang (' . $room->items->count() . ')'">
                @if ($room->items->isEmpty())
                    <div class="p-5"><x-ui.empty-state icon="package" title="Belum ada barang di ruangan ini" /></div>
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
                                    <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
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
                                        <td class="px-4 py-3 text-right">
                                            @if ($ir->condition->value !== 'baik')
                                                <a href="{{ route('pengguna.damage-reports.create', ['room_id' => $room->id, 'item_room_id' => $ir->id]) }}"
                                                class="text-xs text-red-600 hover:text-red-700 hover:underline font-medium">
                                                    Lapor
                                                </a>
                                            @else
                                                <span class="text-xs text-surface-300">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-ui.card>

            {{-- Riwayat Laporan --}}
            <x-ui.card :padding="false" title="Riwayat Laporan">
                @if ($room->damageReports->isEmpty())
                    <div class="p-5"><x-ui.empty-state icon="alert-triangle" title="Belum ada laporan" /></div>
                @else
                    <ul class="divide-y divide-surface-100">
                        @foreach ($room->damageReports as $report)
                            <li class="px-5 py-3 hover:bg-surface-50/60 transition-colors">
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
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <div class="lg:col-span-1 space-y-5">
            <x-ui.card title="Health Score">
                @php
                    $score = $room->health_score;
                    $scoreColor = $score >= 70 ? 'green' : ($score >= 40 ? 'yellow' : 'red');
                @endphp
                <div class="text-center py-2">
                    <div class="relative inline-flex items-center justify-center w-28 h-28 mb-3">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="16" fill="none" class="text-surface-100" stroke="currentColor" stroke-width="2"></circle>
                            <circle cx="18" cy="18" r="16" fill="none" class="text-{{ $scoreColor }}-500" stroke="currentColor" stroke-width="2"
                                    stroke-dasharray="{{ $score * 1.005 }} 100" stroke-linecap="round"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-surface-900 tabular-nums">{{ $score }}%</span>
                        </div>
                    </div>
                    <p class="text-sm text-surface-500">Skor Kondisi</p>
                </div>
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
                </div>
            </x-ui.card>
        </div>
    </div>

@endsection