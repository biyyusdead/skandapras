@extends('layouts.backend')

@section('title', 'Detail Laporan')
@section('page-title', 'Laporan Kerusakan')

@section('content')

    @php
        use App\Enums\DamageReportStatus;
        $s = $report->status;
    @endphp

    <x-ui.page-header :title="'Laporan ' . $report->report_code" subtitle="Review & persetujuan tahap 2">
        <x-slot:actions>
            <x-ui.button href="{{ route('kepala.damage-reports.index') }}" variant="secondary" icon="arrow-left">Kembali</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KIRI --}}
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
                                    @if ($repair->started_at) <p>Mulai: {{ $repair->started_at->format('d M Y') }}</p> @endif
                                    @if ($repair->completed_at) <p>Selesai: {{ $repair->completed_at->format('d M Y') }}</p> @endif
                                    @if ($repair->repair_cost) <p>Biaya: Rp {{ number_format($repair->repair_cost, 0, ',', '.') }}</p> @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif

        </div>

        {{-- KANAN — Aksi --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Approval 2 --}}
            @if ($s === DamageReportStatus::MENUNGGU_PERSETUJUAN_2)
                <x-ui.card title="Persetujuan Tahap 2">
                    <p class="text-xs text-surface-500 mb-3">
                        Setelah disetujui, Waka Sarpras dapat memulai proses perbaikan.
                    </p>

                    @php $appr1 = $report->approvals->where('approval_level', 1)->first(); @endphp
                    @if ($appr1 && $appr1->notes)
                        <div class="p-3 bg-surface-50 rounded-lg mb-3">
                            <p class="text-xs font-medium text-surface-600 mb-1">Catatan Waka Sarpras:</p>
                            <p class="text-xs text-surface-700 italic">"{{ $appr1->notes }}"</p>
                        </div>
                    @endif

                    <form action="{{ route('kepala.approvals.approve', $report) }}" method="POST" class="space-y-3 mb-3">
                        @csrf
                        <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2"
                            placeholder="Tambahkan catatan..."></x-ui.textarea>
                        <x-ui.button type="submit" icon="check-circle" :loading="true" class="w-full">
                            Setujui Laporan
                        </x-ui.button>
                    </form>

                    <form action="{{ route('kepala.approvals.reject', $report) }}" method="POST" class="space-y-3 pt-3 border-t border-surface-100">
                        @csrf
                        <x-ui.textarea name="notes" label="Alasan Penolakan" rows="2" :required="true"
                            placeholder="Alasan penolakan (wajib)..."></x-ui.textarea>
                        <x-ui.button type="submit" variant="danger" icon="x-circle" :loading="true" class="w-full">
                            Tolak Laporan
                        </x-ui.button>
                    </form>
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