@extends('layouts.backend')

@section('title', 'Detail Laporan')
@section('page-title', 'Laporan Kerusakan')

@section('content')

    @php
        use App\Enums\DamageReportStatus;
        $s = $report->status;
    @endphp

    <x-ui.page-header :title="'Laporan ' . $report->report_code" subtitle="Detail & verifikasi laporan">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.damage-reports.index') }}" variant="secondary" icon="arrow-left">Kembali</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">

            {{-- Status --}}
            <x-ui.card title="Status Laporan">
                <div class="flex items-center justify-between mb-4">
                    <x-ui.badge :color="$s->color()" :dot="true" class="!text-sm !px-3 !py-1">
                        {{ $s->label() }}
                    </x-ui.badge>
                    <span class="text-xs text-surface-500">{{ $report->reported_at?->format('d M Y H:i') }}</span>
                </div>

                {{-- Approval Progress --}}
                @if ($report->approvals->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-surface-100 space-y-3">
                        @foreach ($report->approvals as $approval)
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0
                                            {{ $approval->status->value === 'approved' ? 'bg-green-100 text-green-600' : ($approval->status->value === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-600') }}">
                                    <i data-lucide="{{ $approval->status->value === 'approved' ? 'check' : ($approval->status->value === 'rejected' ? 'x' : 'clock') }}" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-medium text-surface-800">
                                        Tahap {{ $approval->approval_level }} — {{ $approval->levelLabel() }}
                                    </p>
                                    <p class="text-xs text-surface-500 mt-0.5">
                                        {{ $approval->approver->name }} · {{ $approval->approved_at?->format('d M Y H:i') ?? 'Menunggu' }}
                                    </p>
                                    @if ($approval->notes)
                                        <p class="text-xs text-surface-600 mt-1 italic">"{{ $approval->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- Detail Kerusakan --}}
            <x-ui.card title="Detail Kerusakan">
                <dl class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Ruangan</dt>
                            <dd class="text-sm text-surface-800">{{ $report->room->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Pelapor</dt>
                            <dd class="text-sm text-surface-800">{{ $report->reporter?->name ?? 'Pengunjung' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Objek Kerusakan</dt>
                        <dd class="text-sm text-surface-800">{{ $report->itemRoom?->item?->name ?? 'Ruangan (umum)' }}</dd>
                    </div>
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

            {{-- Repair Info --}}
            @if ($report->repairReports->isNotEmpty())
                <x-ui.card :padding="false" title="Info Perbaikan">
                    <ul class="divide-y divide-surface-100">
                        @foreach ($report->repairReports as $repair)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <p class="text-sm font-medium text-surface-800">{{ $repair->technician_name }}</p>
                                    <x-ui.badge :color="$repair->status->color()" :dot="true">{{ $repair->status->label() }}</x-ui.badge>
                                </div>
                                @if ($repair->repair_description)
                                    <p class="text-sm text-surface-700">{{ $repair->repair_description }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        {{-- Kolom Kanan — Aksi --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Aksi Verifikasi --}}
            @if ($s === DamageReportStatus::MENUNGGU_VERIFIKASI)
                <x-ui.card title="Verifikasi Laporan">
                    <form action="{{ route('waka.damage-reports.verify', $report) }}" method="POST" class="space-y-3">
                        @csrf
                        <x-ui.textarea name="verification_note" label="Catatan Verifikasi (opsional)" rows="3"
                            placeholder="Tambahkan catatan..."></x-ui.textarea>
                        <x-ui.button type="submit" icon="check" :loading="true" class="w-full">
                            Verifikasi Laporan
                        </x-ui.button>
                    </form>
                </x-ui.card>
            @endif

            {{-- Aksi Approval 1 --}}
            @if (in_array($s, [DamageReportStatus::DIVERIFIKASI, DamageReportStatus::MENUNGGU_PERSETUJUAN_1]))
                <x-ui.card title="Persetujuan Tahap 1">
                    <p class="text-xs text-surface-500 mb-3">
                        Setelah disetujui, laporan akan diteruskan ke Kepala Sekolah untuk persetujuan tahap 2.
                    </p>

                    <form action="{{ route('waka.damage-reports.approve', $report) }}" method="POST" class="space-y-3 mb-3">
                        @csrf
                        <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2"
                            placeholder="Catatan untuk Kepala Sekolah..."></x-ui.textarea>
                        <x-ui.button type="submit" icon="check-circle" :loading="true" class="w-full">
                            Setujui Laporan
                        </x-ui.button>
                    </form>

                    <form action="{{ route('waka.damage-reports.reject', $report) }}" method="POST" class="space-y-3 pt-3 border-t border-surface-100">
                        @csrf
                        <x-ui.textarea name="notes" label="Alasan Penolakan" rows="2" :required="true"
                            placeholder="Alasan penolakan (wajib)..."></x-ui.textarea>
                        <x-ui.button type="submit" variant="danger" icon="x-circle" :loading="true" class="w-full">
                            Tolak Laporan
                        </x-ui.button>
                    </form>
                </x-ui.card>
            @endif

            {{-- CTA ke Repair --}}
            @if ($s === DamageReportStatus::DISETUJUI || $s === DamageReportStatus::SIAP_DIPERBAIKI)
                <x-ui.card title="Proses Perbaikan">
                    <p class="text-xs text-surface-500 mb-3">
                        Laporan sudah disetujui kedua tahap. Anda dapat mencatat proses perbaikan.
                    </p>
                    <x-ui.button href="{{ route('waka.repair-reports.create', $report) }}"
                                 icon="wrench" class="w-full" :loading="false">
                        Mulai Perbaikan
                    </x-ui.button>
                </x-ui.card>
            @endif

            {{-- Info --}}
            <x-ui.card title="Info">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Kode Laporan</dt>
                        <dd class="font-mono text-surface-800">{{ $report->report_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Ruangan</dt>
                        <dd class="text-surface-800">{{ $report->room->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Pelapor</dt>
                        <dd class="text-surface-800">{{ $report->reporter?->name ?? 'Pengunjung' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Tanggal Laporan</dt>
                        <dd class="text-surface-800">{{ $report->reported_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

        </div>
    </div>

@endsection