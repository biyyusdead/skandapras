@extends('layouts.backend')

@section('title', 'Detail Laporan')
@section('page-title', 'Laporan Saya')

@section('content')

    <x-ui.page-header :title="'Laporan ' . $report->report_code" subtitle="Detail laporan kerusakan">
        <x-slot:actions>
            <x-ui.button href="{{ route('pengguna.damage-reports.index') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Kolom Kiri --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Status Timeline --}}
            <x-ui.card title="Status Laporan">
                <div class="flex items-center justify-between mb-4">
                    <x-ui.badge :color="$report->status->color()" :dot="true">
                        {{ $report->status->label() }}
                    </x-ui.badge>
                    <span class="text-xs text-surface-500">{{ $report->reported_at?->format('d M Y H:i') }}</span>
                </div>

                {{-- Timeline visual --}}
                @php
                    $steps = [
                        ['key' => 'menunggu_verifikasi', 'label' => 'Diajukan', 'icon' => 'send'],
                        ['key' => 'diverifikasi', 'label' => 'Diverifikasi', 'icon' => 'check'],
                        ['key' => 'menunggu_persetujuan_2', 'label' => 'Persetujuan', 'icon' => 'check-circle'],
                        ['key' => 'disetujui', 'label' => 'Disetujui', 'icon' => 'shield-check'],
                        ['key' => 'diproses', 'label' => 'Diproses', 'icon' => 'wrench'],
                        ['key' => 'selesai', 'label' => 'Selesai', 'icon' => 'check-check'],
                    ];
                    $statusOrder = ['menunggu_verifikasi' => 1, 'diverifikasi' => 2, 'menunggu_persetujuan_1' => 2, 'menunggu_persetujuan_2' => 3, 'disetujui' => 4, 'siap_diperbaiki' => 4, 'diproses' => 5, 'selesai' => 6, 'ditolak' => 99];
                    $currentStep = $statusOrder[$report->status->value] ?? 0;
                    $isRejected = $report->status->value === 'ditolak';
                @endphp

                @if ($isRejected)
                    <div class="flex items-start gap-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mt-0.5 shrink-0"></i>
                        <div>
                            <p class="text-sm font-medium text-red-800">Laporan Ditolak</p>
                            @php
                                $rejectedApproval = $report->approvals->where('status', 'rejected')->first();
                            @endphp
                            @if ($rejectedApproval?->notes)
                                <p class="text-xs text-red-700 mt-1">{{ $rejectedApproval->notes }}</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-1 overflow-x-auto pb-2">
                        @foreach ($steps as $i => $step)
                            @php $stepNum = $i + 1; @endphp
                            <div class="flex items-center gap-1 shrink-0">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                {{ $stepNum <= $currentStep ? 'bg-primary-600 text-white' : 'bg-surface-100 text-surface-400' }}">
                                        <i data-lucide="{{ $step['icon'] }}" class="w-3.5 h-3.5"></i>
                                    </div>
                                    <p class="text-[10px] mt-1 {{ $stepNum <= $currentStep ? 'text-primary-700 font-medium' : 'text-surface-400' }}">
                                        {{ $step['label'] }}
                                    </p>
                                </div>
                                @if (!$loop->last)
                                    <div class="w-8 h-0.5 mb-4 {{ $stepNum < $currentStep ? 'bg-primary-600' : 'bg-surface-200' }}"></div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- Detail Kerusakan --}}
            <x-ui.card title="Detail Kerusakan">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Ruangan</dt>
                        <dd class="text-sm text-surface-800">{{ $report->room->name }} <span class="text-surface-400 font-mono text-xs">({{ $report->room->code }})</span></dd>
                    </div>
                    @if ($report->item)
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Objek</dt>
                            <dd class="text-sm text-surface-800">{{ $report->item->name }} <span class="text-surface-400 font-mono text-xs">({{ $report->item->code }})</span></dd>
                        </div>
                    @else
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Objek</dt>
                            <dd class="text-sm text-surface-600 italic"> {{ $report->itemRoom?->item?->name ?? 'Ruangan (umum)' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Deskripsi</dt>
                        <dd class="text-sm text-surface-700 leading-relaxed whitespace-pre-line">{{ $report->description }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Foto Bukti --}}
            @if ($report->evidence_photo)
                <x-ui.card title="Foto Bukti">
                    <img src="{{ asset('storage/' . $report->evidence_photo) }}" alt="Bukti"
                         class="rounded-lg w-full max-w-md">
                </x-ui.card>
            @endif

            {{-- Catatan Verifikasi --}}
            @if ($report->verification_note)
                <x-ui.card title="Catatan Verifikasi">
                    <p class="text-sm text-surface-700 leading-relaxed">{{ $report->verification_note }}</p>
                    @if ($report->verifier)
                        <p class="text-xs text-surface-400 mt-2">
                            Oleh {{ $report->verifier->name }} · {{ $report->verified_at?->format('d M Y H:i') }}
                        </p>
                    @endif
                </x-ui.card>
            @endif

            {{-- Riwayat Approval --}}
            @if ($report->approvals->isNotEmpty())
                <x-ui.card :padding="false" title="Riwayat Persetujuan">
                    <ul class="divide-y divide-surface-100">
                        @foreach ($report->approvals as $approval)
                            <li class="px-5 py-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-medium text-surface-800">
                                            Persetujuan Tahap {{ $approval->approval_level }}
                                            <span class="text-xs font-normal text-surface-500">({{ $approval->levelLabel() }})</span>
                                        </p>
                                        <p class="text-xs text-surface-500 mt-0.5">
                                            {{ $approval->approver->name }} ·
                                            {{ $approval->approved_at?->format('d M Y H:i') ?? 'Menunggu' }}
                                        </p>
                                        @if ($approval->notes)
                                            <p class="text-xs text-surface-600 mt-2 italic">"{{ $approval->notes }}"</p>
                                        @endif
                                    </div>
                                    <x-ui.badge :color="$approval->status->color()" :dot="true">{{ $approval->status->label() }}</x-ui.badge>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

            {{-- Info Perbaikan --}}
            @if ($report->repairReports->isNotEmpty())
                <x-ui.card :padding="false" title="Info Perbaikan">
                    <ul class="divide-y divide-surface-100">
                        @foreach ($report->repairReports as $repair)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-sm font-medium text-surface-800">{{ $repair->technician_name }}</p>
                                        @if ($repair->technician_contact)
                                            <p class="text-xs text-surface-500">{{ $repair->technician_contact }}</p>
                                        @endif
                                    </div>
                                    <x-ui.badge :color="$repair->status->color()" :dot="true">{{ $repair->status->label() }}</x-ui.badge>
                                </div>
                                @if ($repair->repair_description)
                                    <p class="text-sm text-surface-700 mb-2">{{ $repair->repair_description }}</p>
                                @endif
                                <div class="grid grid-cols-2 gap-3 text-xs text-surface-500">
                                    @if ($repair->started_at)
                                        <p>Mulai: {{ $repair->started_at->format('d M Y') }}</p>
                                    @endif
                                    @if ($repair->completed_at)
                                        <p>Selesai: {{ $repair->completed_at->format('d M Y') }}</p>
                                    @endif
                                    @if ($repair->repair_cost)
                                        <p>Biaya: Rp {{ number_format($repair->repair_cost, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        {{-- Kolom Kanan --}}
        <div class="lg:col-span-1 space-y-5">
            <x-ui.card title="Info Laporan">
                <dl class="space-y-3">
                    <div>
                        <dt class="text-xs font-medium text-surface-500 mb-0.5">Kode Laporan</dt>
                        <dd class="text-sm font-mono text-surface-800">{{ $report->report_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 mb-0.5">Pelapor</dt>
                        <dd class="text-sm text-surface-800">{{ $report->reporter?->name ?? 'Pengunjung' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 mb-0.5">Tanggal Laporan</dt>
                        <dd class="text-sm text-surface-800">{{ $report->reported_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            @if ($report->status->value === 'menunggu_verifikasi')
                <x-ui.card title="Aksi">
                    <form id="delete-form" method="POST" action="{{ route('pengguna.damage-reports.destroy', $report) }}">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="danger" icon="trash-2" class="w-full"
                            onclick="return confirm('Hapus laporan ini?')">
                            Hapus Laporan
                        </x-ui.button>
                    </form>
                </x-ui.card>
            @endif
        </div>
    </div>

@endsection