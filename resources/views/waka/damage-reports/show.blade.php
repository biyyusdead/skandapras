@extends('layouts.backend')

@section('title', 'Detail Laporan')
@section('page-title', 'Laporan Kerusakan')

@section('content')

    @php
        use App\Enums\DamageReportStatus;
        use App\Enums\IdentityStatus;
        use App\Enums\ValidityStatus;
        use App\Enums\DamageCategory;
        use App\Enums\RepairStatus;
        $s = $report->status;
        $activeRepair = $report->repairReports->firstWhere('status.value', RepairStatus::IN_PROGRESS->value)
            ?? $report->repairReports->firstWhere('status.value', RepairStatus::READY->value);
    @endphp

    <x-ui.page-header :title="'Laporan ' . $report->report_code" subtitle="Detail, verifikasi identitas & alur persetujuan perbaikan">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.damage-reports.index') }}" variant="secondary" icon="arrow-left">Kembali</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div x-data="{ openCompleteModal: false }" class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- KOLOM KIRI (2 Kolom) --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- 3 Aspek Status Card --}}
            <x-ui.card title="Status Pengaduan & Alur Verifikasi">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                    {{-- 1. Status Identitas --}}
                    <div class="p-3 rounded-xl border border-surface-200 bg-surface-50/50">
                        <span class="block text-[11px] font-semibold text-surface-500 uppercase tracking-wider mb-1">1. Verifikasi Identitas</span>
                        @if ($report->identity_status)
                            <x-ui.badge :color="$report->identity_status->color()" :dot="true" class="!text-xs">
                                {{ $report->identity_status->label() }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge color="yellow" :dot="true" class="!text-xs">Menunggu Verifikasi</x-ui.badge>
                        @endif
                    </div>

                    {{-- 2. Status Validitas --}}
                    <div class="p-3 rounded-xl border border-surface-200 bg-surface-50/50">
                        <span class="block text-[11px] font-semibold text-surface-500 uppercase tracking-wider mb-1">2. Validitas Laporan</span>
                        @if ($report->validity_status)
                            <x-ui.badge :color="$report->validity_status->color()" :dot="true" class="!text-xs">
                                {{ $report->validity_status->label() }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge color="yellow" :dot="true" class="!text-xs">Menunggu Pemeriksaan</x-ui.badge>
                        @endif
                    </div>

                    {{-- 3. Status Persetujuan & Perbaikan --}}
                    <div class="p-3 rounded-xl border border-surface-200 bg-surface-50/50">
                        <span class="block text-[11px] font-semibold text-surface-500 uppercase tracking-wider mb-1">3. Status Alur</span>
                        <x-ui.badge :color="$s->color()" :dot="true" class="!text-xs">
                            {{ $s->label() }}
                        </x-ui.badge>
                    </div>
                </div>

                {{-- Kategori Kerusakan jika sudah ditentukan --}}
                @if ($report->damage_category)
                    <div class="p-3 rounded-xl bg-violet-50 border border-violet-200 mb-4 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-[#5B3BA5] block">Tingkat Kerusakan:</span>
                            <span class="text-sm font-bold text-slate-800">{{ $report->damage_category->label() }}</span>
                            @if ($report->category_note)
                                <p class="text-xs text-slate-600 mt-0.5 italic">"{{ $report->category_note }}"</p>
                            @endif
                        </div>
                        <x-ui.badge :color="$report->damage_category->color()">
                            {{ $report->damage_category->label() }}
                        </x-ui.badge>
                    </div>
                @endif

                {{-- Riwayat Alasan Penolakan jika ditolak --}}
                @if ($report->rejection_reason)
                    <div class="p-3.5 rounded-xl bg-red-50 border border-red-200 mb-4">
                        <span class="text-xs font-bold text-red-800 flex items-center gap-1.5 mb-1">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                            Alasan Penolakan:
                        </span>
                        <p class="text-sm text-red-700 leading-relaxed">{{ $report->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Approval Progress --}}
                @if ($report->approvals->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-surface-100 space-y-3">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-surface-500 mb-2">Riwayat Keputusan & Persetujuan</h4>
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
                                        {{ $approval->approver->name }} · {{ $approval->approved_at?->format('d M Y H:i') ?? 'Menunggu Keputusan' }}
                                    </p>
                                    @if ($approval->notes)
                                        <p class="text-xs text-surface-600 mt-1 italic bg-surface-50 p-2 rounded border border-surface-100">"{{ $approval->notes }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-ui.card>

            {{-- Detail Identitas Pelapor --}}
            <x-ui.card title="Identitas Pelapor">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-0.5">Nama Pelapor</dt>
                        <dd class="text-sm font-semibold text-surface-800">{{ $report->displayName() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-0.5">Status Pelapor</dt>
                        <dd class="text-sm font-semibold text-surface-800">
                            {{ $report->reporter_type?->label() ?? 'Pengunjung Umum' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-0.5">Kontak / Telepon</dt>
                        <dd class="text-sm text-surface-800 font-mono">
                            {{ $report->maskedPhone() }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-0.5">Nomor Identitas</dt>
                        <dd class="text-sm text-surface-800 font-mono">
                            {{ $report->maskedIdentifier() }}
                        </dd>
                    </div>
                    @if ($report->student_class || $report->student_major)
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-0.5">Kelas & Jurusan (Siswa)</dt>
                            <dd class="text-sm text-surface-800">
                                {{ $report->student_class ?? '—' }} {{ $report->student_major ? '· ' . $report->student_major : '' }}
                            </dd>
                        </div>
                    @endif
                    @if ($report->identity_note)
                        <div class="sm:col-span-2 p-3 bg-surface-50 rounded-lg border border-surface-200">
                            <dt class="text-xs font-semibold text-surface-600 mb-1">Catatan Verifikasi / Klarifikasi Identitas:</dt>
                            <dd class="text-xs text-surface-700 leading-relaxed">{{ $report->identity_note }}</dd>
                        </div>
                    @endif
                </dl>
            </x-ui.card>

            {{-- Detail Kerusakan --}}
            <x-ui.card title="Detail Kerusakan">
                <dl class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Ruangan</dt>
                            <dd class="text-sm font-medium text-surface-800">{{ $report->room->name }} ({{ $report->room->code }})</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Objek Kerusakan</dt>
                            <dd class="text-sm font-medium text-surface-800">{{ $report->itemRoom?->item?->name ?? 'Ruangan (secara umum)' }}</dd>
                        </div>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Deskripsi Kerusakan</dt>
                        <dd class="text-sm text-surface-700 leading-relaxed whitespace-pre-line bg-surface-50 p-3.5 rounded-xl border border-surface-100">{{ $report->description }}</dd>
                    </div>
                </dl>
            </x-ui.card>

            {{-- Foto Bukti --}}
            @if ($report->evidence_photo)
                <x-ui.card title="Foto Bukti Kerusakan">
                    <div class="flex justify-center p-2 bg-surface-50 rounded-xl border border-surface-200">
                        <img src="{{ asset('storage/' . $report->evidence_photo) }}" alt="Bukti Kerusakan"
                             class="rounded-lg max-h-[380px] w-auto object-contain shadow-sm">
                    </div>
                </x-ui.card>
            @endif

            {{-- Info Perbaikan & Riwayat Teknisi --}}
            @if ($report->repairReports->isNotEmpty())
                <x-ui.card :padding="false" title="Catatan Riwayat Perbaikan (Petugas)">
                    <ul class="divide-y divide-surface-100">
                        @foreach ($report->repairReports as $repair)
                            <li class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div>
                                        <p class="text-sm font-semibold text-surface-800">{{ $repair->technician_name }}</p>
                                        @if ($repair->technician_contact)
                                            <p class="text-xs text-surface-500">{{ $repair->technician_contact }}</p>
                                        @endif
                                    </div>
                                    <x-ui.badge :color="$repair->status->color()" :dot="true">{{ $repair->status->label() }}</x-ui.badge>
                                </div>
                                @if ($repair->repair_description)
                                    <p class="text-sm text-surface-700 mb-2 leading-relaxed">{{ $repair->repair_description }}</p>
                                @endif
                                <div class="grid grid-cols-2 gap-3 text-xs text-surface-500 mt-2">
                                    @if ($repair->started_at) <p>Mulai: {{ $repair->started_at->format('d M Y') }}</p> @endif
                                    @if ($repair->completed_at) <p>Selesai: {{ $repair->completed_at->format('d M Y') }}</p> @endif
                                    @if ($repair->repair_cost) <p>Biaya: Rp {{ number_format($repair->repair_cost, 0, ',', '.') }}</p> @endif
                                </div>

                                {{-- Foto Bukti Perbaikan --}}
                                @if ($repair->repair_evidence_before || $repair->repair_evidence_after)
                                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-surface-100">
                                        @if ($repair->repair_evidence_before)
                                            <div>
                                                <p class="text-[10px] font-semibold text-surface-500 mb-1">Foto Sebelum:</p>
                                                <a href="{{ asset('storage/' . $repair->repair_evidence_before) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $repair->repair_evidence_before) }}" class="h-16 w-24 object-cover rounded-lg border border-surface-200 hover:opacity-90 transition">
                                                </a>
                                            </div>
                                        @endif
                                        @if ($repair->repair_evidence_after)
                                            <div>
                                                <p class="text-[10px] font-semibold text-emerald-700 mb-1">Foto Setelah:</p>
                                                <a href="{{ asset('storage/' . $repair->repair_evidence_after) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $repair->repair_evidence_after) }}" class="h-16 w-24 object-cover rounded-lg border border-emerald-300 ring-2 ring-emerald-500/20 hover:opacity-90 transition">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </x-ui.card>
            @endif
        </div>

        {{-- KOLOM KANAN — AKSI WAKA --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- TAHAP 1: VERIFIKASI IDENTITAS PELAPOR --}}
            @if (!in_array($s, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK]))
                <x-ui.card title="1. Verifikasi Identitas">
                    @if ($report->identity_status === IdentityStatus::TERVERIFIKASI)
                        <div class="flex items-center gap-2 text-xs text-green-700 bg-green-50 p-3 rounded-lg border border-green-200">
                            <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-green-600"></i>
                            <span>Identitas pelapor telah diverifikasi.</span>
                        </div>
                    @else
                        <p class="text-xs text-surface-500 mb-3">
                            Periksa nomor identitas, kontak, dan kewajaran pelapor sebelum melanjutkan.
                        </p>

                        <form action="{{ route('waka.damage-reports.verify-identity', $report) }}" method="POST" class="space-y-3 mb-3">
                            @csrf
                            <x-ui.textarea name="identity_note" label="Catatan Verifikasi (opsional)" rows="2"
                                placeholder="Misal: Identitas siswa telah dicek wali kelas..."></x-ui.textarea>
                            <x-ui.button type="submit" icon="user-check" variant="primary" :loading="true" class="w-full">
                                Verifikasi Identitas
                            </x-ui.button>
                        </form>

                        <form action="{{ route('waka.damage-reports.request-clarification', $report) }}" method="POST" class="space-y-3 pt-3 border-t border-surface-100">
                            @csrf
                            <x-ui.textarea name="clarification_note" label="Minta Klarifikasi" rows="2" :required="true"
                                placeholder="Tuliskan hal yang perlu diklarifikasi..."></x-ui.textarea>
                            <x-ui.button type="submit" variant="secondary" icon="help-circle" :loading="true" class="w-full">
                                Minta Klarifikasi
                            </x-ui.button>
                        </form>
                    @endif
                </x-ui.card>

                {{-- TAHAP 2: VALIDASI LAPORAN --}}
                <x-ui.card title="2. Validitas Pengaduan">
                    @if ($report->validity_status === ValidityStatus::VALID)
                        <div class="flex items-center gap-2 text-xs text-emerald-700 bg-emerald-50 p-3 rounded-lg border border-emerald-200">
                            <i data-lucide="check-check" class="w-4 h-4 shrink-0 text-emerald-600"></i>
                            <span>Laporan telah diperiksa & dinyatakan VALID.</span>
                        </div>
                    @else
                        <p class="text-xs text-surface-500 mb-3">
                            Pastikan kondisi bukti kerusakan sarana/prasarana benar adanya.
                        </p>

                        <form action="{{ route('waka.damage-reports.validate', $report) }}" method="POST" class="space-y-3 mb-3">
                            @csrf
                            <x-ui.textarea name="verification_note" label="Catatan Pemeriksaan (opsional)" rows="2"
                                placeholder="Catatan kondisi fisik sarpras..."></x-ui.textarea>
                            <x-ui.button type="submit" icon="check" variant="primary" :loading="true" class="w-full">
                                Nyatakan Laporan Valid
                            </x-ui.button>
                        </form>

                        <form action="{{ route('waka.damage-reports.reject', $report) }}" method="POST" class="space-y-3 pt-3 border-t border-surface-100">
                            @csrf
                            <x-ui.textarea name="notes" label="Alasan Penolakan" rows="2" :required="true"
                                placeholder="Tulis alasan penolakan laporan..."></x-ui.textarea>
                            <x-ui.button type="submit" variant="danger" icon="x-circle" :loading="true" class="w-full">
                                Tolak Laporan
                            </x-ui.button>
                        </form>
                    @endif
                </x-ui.card>

                {{-- TAHAP 3: PERSETUJUAN BERDASARKAN TINGKAT KERUSAKAN --}}
                @if ($report->identity_status === IdentityStatus::TERVERIFIKASI && $report->validity_status === ValidityStatus::VALID && in_array($s, [DamageReportStatus::DIVERIFIKASI, DamageReportStatus::MENUNGGU_VERIFIKASI, DamageReportStatus::MENUNGGU_PERSETUJUAN_1]))
                    <x-ui.card title="3. Alur Persetujuan Perbaikan">
                        <p class="text-xs text-surface-600 mb-4">
                            Pilih kategori kerusakan untuk menentukan jalur persetujuan:
                        </p>

                        <div class="space-y-4">
                            {{-- Jalur A: Kerusakan Ringan --}}
                            <div class="p-3.5 rounded-xl border border-teal-200 bg-teal-50/50 space-y-2.5">
                                <div class="flex items-center gap-2 text-teal-800 font-semibold text-xs">
                                    <i data-lucide="wrench" class="w-4 h-4 text-teal-600"></i>
                                    <span>Jalur A — Kerusakan Ringan</span>
                                </div>
                                <p class="text-[11px] text-teal-700 leading-relaxed">
                                    Disetujui langsung oleh Waka Sarpras tanpa perlu persetujuan Kepala Sekolah. Langsung dapat ditugaskan ke petugas.
                                </p>
                                <form action="{{ route('waka.damage-reports.approve-minor', $report) }}" method="POST" class="space-y-2 pt-1">
                                    @csrf
                                    <input type="hidden" name="damage_category" value="ringan">
                                    <x-ui.textarea name="notes" placeholder="Catatan persetujuan (opsional)" rows="2"></x-ui.textarea>
                                    <x-ui.button type="submit" icon="check-circle" :loading="true" class="w-full !bg-teal-600 hover:!bg-teal-700 !text-white">
                                        Setujui & Tugaskan
                                    </x-ui.button>
                                </form>
                            </div>

                            {{-- Jalur B: Kerusakan Besar --}}
                            <div class="p-3.5 rounded-xl border border-rose-200 bg-rose-50/50 space-y-2.5">
                                <div class="flex items-center gap-2 text-rose-800 font-semibold text-xs">
                                    <i data-lucide="shield-alert" class="w-4 h-4 text-rose-600"></i>
                                    <span>Jalur B — Kerusakan Besar</span>
                                </div>
                                <p class="text-[11px] text-rose-700 leading-relaxed">
                                    Memerlukan persetujuan Kepala Sekolah (Tahap 2) sebelum penugasan perbaikan dapat dimulai.
                                </p>
                                <form action="{{ route('waka.damage-reports.submit-major', $report) }}" method="POST" class="space-y-2 pt-1">
                                    @csrf
                                    <input type="hidden" name="damage_category" value="berat">
                                    <x-ui.textarea name="notes" placeholder="Catatan pengajuan ke Kepala Sekolah (opsional)" rows="2"></x-ui.textarea>
                                    <x-ui.button type="submit" icon="send" :loading="true" class="w-full !bg-rose-600 hover:!bg-rose-700 !text-white">
                                        Ajukan Persetujuan Kepala Sekolah
                                    </x-ui.button>
                                </form>
                            </div>
                        </div>
                    </x-ui.card>
                @elseif (!in_array($s, [DamageReportStatus::DISETUJUI, DamageReportStatus::SIAP_DIPERBAIKI, DamageReportStatus::DIPROSES, DamageReportStatus::SELESAI, DamageReportStatus::MENUNGGU_PERSETUJUAN_2]))
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800 flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <span>Laporan belum dapat diproses ke tahap persetujuan sebelum identitas pelapor diverifikasi dan laporan dinyatakan valid.</span>
                    </div>
                @endif
            @endif

            {{-- MENUNGGU KEPALA SEKOLAH --}}
            @if ($s === DamageReportStatus::MENUNGGU_PERSETUJUAN_2)
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 space-y-2">
                    <div class="flex items-center gap-2 text-blue-800 font-semibold text-sm">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
                        <span>Menunggu Persetujuan Kepala Sekolah</span>
                    </div>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        Laporan kerusakan besar ini telah diajukan ke Kepala Sekolah dan sedang menunggu keputusan persetujuan.
                    </p>
                </div>
            @endif

            {{-- CTA KE PERBAIKAN / PENUGASAN PETUGAS --}}
            @if ($report->canBeRepaired() && in_array($s, [DamageReportStatus::DISETUJUI, DamageReportStatus::SIAP_DIPERBAIKI]))
                <x-ui.card title="Penugasan Petugas Perbaikan">
                    <p class="text-xs text-surface-600 mb-3">
                        Laporan telah memenuhi seluruh syarat persetujuan. Anda dapat menugaskan petugas untuk memulai perbaikan.
                    </p>
                    <x-ui.button href="{{ route('waka.repair-reports.create', $report) }}"
                                 icon="wrench" class="w-full" :loading="false">
                        Tugaskan Petugas Perbaikan
                    </x-ui.button>
                </x-ui.card>
            @endif

            {{-- TAHAP 4: PENYELESAIAN PERBAIKAN --}}
            @if ($s === DamageReportStatus::DIPROSES && $activeRepair)
                <x-ui.card title="Penyelesaian Perbaikan">
                    <div class="space-y-3">
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 space-y-1">
                            <p class="font-semibold flex items-center gap-1.5 text-amber-950">
                                <i data-lucide="wrench" class="w-4 h-4 text-amber-600"></i>
                                <span>Perbaikan Sedang Berlangsung</span>
                            </p>
                            <p class="text-amber-800">Petugas: <strong>{{ $activeRepair->technician_name }}</strong></p>
                            @if ($activeRepair->started_at)
                                <p class="text-amber-700 text-[11px]">Dimulai: {{ $activeRepair->started_at->format('d M Y') }}</p>
                            @endif
                        </div>

                        <p class="text-xs text-surface-600 leading-relaxed">
                            Pekerjaan perbaikan fisik telah selesai? Anda dapat langsung mengunggah foto bukti dan menandainya selesai di sini tanpa perlu berpindah halaman.
                        </p>

                        <button type="button" @click="openCompleteModal = true"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] transition shadow-sm">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Tandai Perbaikan Selesai</span>
                        </button>

                        <a href="{{ route('waka.repair-reports.edit', $activeRepair) }}"
                           class="block text-center text-xs text-primary-600 hover:text-primary-700 font-medium hover:underline pt-1">
                            Buka Form Edit Perbaikan Lengkap &rarr;
                        </a>
                    </div>
                </x-ui.card>
            @endif

            {{-- Info Ringkas --}}
            <x-ui.card title="Informasi Laporan">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Kode Laporan</dt>
                        <dd class="font-mono text-surface-800 font-medium">{{ $report->report_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Ruangan</dt>
                        <dd class="text-surface-800 font-medium">{{ $report->room->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Pelapor</dt>
                        <dd class="text-surface-800 font-medium">{{ $report->displayName() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-surface-500 mb-0.5">Tanggal Laporan</dt>
                        <dd class="text-surface-800">{{ $report->reported_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </x-ui.card>

        </div>
    </div>

    {{-- MODAL SELESAIKAN PERBAIKAN CEPAT --}}
    @if ($activeRepair)
        <div x-cloak x-show="openCompleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4"
             role="dialog" aria-modal="true" style="display: none;">
            {{-- Backdrop --}}
            <div x-show="openCompleteModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="openCompleteModal = false"
                 class="fixed inset-0 bg-surface-900/50 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div x-show="openCompleteModal"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden p-6 z-10 space-y-5">

                <div class="flex items-center justify-between border-b border-surface-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
                            <i data-lucide="check-check" class="w-5 h-5"></i>
                        </span>
                        <div>
                            <h3 class="font-bold text-surface-900 text-base">Selesaikan Perbaikan</h3>
                            <p class="text-xs text-surface-500">Laporan: {{ $report->report_code }}</p>
                        </div>
                    </div>
                    <button type="button" @click="openCompleteModal = false" class="text-surface-400 hover:text-surface-600 p-1">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <form action="{{ route('waka.repair-reports.complete', $activeRepair) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-surface-700 mb-1">
                            Foto Bukti Setelah Perbaikan <span class="text-emerald-600">*</span>
                        </label>
                        <input type="file" name="repair_evidence_after" accept="image/*" required
                               class="w-full text-xs text-surface-700 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-surface-300 rounded-lg p-1.5 bg-surface-50">
                        <p class="text-[11px] text-surface-500 mt-1">Unggah foto bukti hasil perbaikan fisik (JPG, PNG, atau WebP, maks 5MB).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-surface-700 mb-1">Tanggal Selesai</label>
                            <input type="date" name="completed_at" value="{{ date('Y-m-d') }}"
                                   class="w-full text-xs px-3 py-2 border border-surface-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-surface-700 mb-1">Biaya Riil Perbaikan (Rp)</label>
                            <input type="number" name="repair_cost" value="{{ $activeRepair->repair_cost }}" min="0" placeholder="0"
                                   class="w-full text-xs px-3 py-2 border border-surface-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-surface-700 mb-1">Deskripsi Tindakan yang Dilakukan</label>
                        <textarea name="repair_description" rows="3" placeholder="Jelaskan tindakan teknis atau penggantian komponen yang telah selesai dilakukan..."
                                  class="w-full text-xs px-3 py-2 border border-surface-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">{{ $activeRepair->repair_description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-surface-700 mb-1">Catatan Tambahan (opsional)</label>
                        <input type="text" name="notes" value="{{ $activeRepair->notes }}" placeholder="Catatan untuk Waka/Kepsek..."
                               class="w-full text-xs px-3 py-2 border border-surface-300 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-100">
                        <button type="button" @click="openCompleteModal = false"
                                class="px-3.5 py-2 text-xs font-medium text-surface-600 bg-surface-100 hover:bg-surface-200 rounded-lg transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm transition">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Simpan &amp; Tandai Selesai</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

@endsection