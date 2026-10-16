<?php

namespace App\Http\Controllers\Waka;

use App\Enums\ApprovalStatus;
use App\Enums\DamageCategory;
use App\Enums\DamageReportStatus;
use App\Enums\IdentityStatus;
use App\Enums\UserRole;
use App\Enums\ValidityStatus;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\DamageReport;
use App\Models\Room;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        $reports = DamageReport::query()
            ->with(['room:id,name,code', 'itemRoom.item:id,name', 'reporter:id,name'])
            ->when($request->search, fn ($q, $s) => $q->where('report_code', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->when($request->room_id, fn ($q, $r) => $q->where('room_id', $r))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $statsRow = DamageReport::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(CASE WHEN status = 'menunggu_verifikasi' THEN 1 END) as menunggu")
            ->selectRaw("COUNT(CASE WHEN status IN ('disetujui', 'siap_diperbaiki', 'diproses') THEN 1 END) as diproses")
            ->selectRaw("COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai")
            ->first();

        $stats = [
            'total' => (int) ($statsRow->total ?? 0),
            'menunggu' => (int) ($statsRow->menunggu ?? 0),
            'diproses' => (int) ($statsRow->diproses ?? 0),
            'selesai' => (int) ($statsRow->selesai ?? 0),
        ];

        return view('waka.damage-reports.index', [
            'reports' => $reports,
            'statuses' => DamageReportStatus::cases(),
            'stats' => $stats,
            'rooms' => Room::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function show(DamageReport $report): View
    {
        $report->load([
            'room:id,name,code,photo',
            'itemRoom.item:id,name,code',
            'reporter:id,name,email',
            'verifier:id,name',
            'approvals.approver:id,name',
            'repairReports.creator:id,name',
        ]);

        return view('waka.damage-reports.show', compact('report'));
    }

    /**
     * Verifikasi identitas pelapor (manual).
     */
    public function verifyIdentity(Request $request, DamageReport $report): RedirectResponse
    {
        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final dan tidak dapat diubah status identitasnya.');
        }

        $validated = $request->validate([
            'identity_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'identity_status' => IdentityStatus::TERVERIFIKASI,
            'identity_note' => $validated['identity_note'] ?? 'Identitas telah diverifikasi secara manual oleh Waka Sarpras.',
        ]);

        $this->logActivity('verify_identity', 'damage-reports', "Memverifikasi identitas pelapor pada laporan: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Identitas pelapor berhasil diverifikasi.');
    }

    /**
     * Minta klarifikasi identitas kepada pelapor.
     */
    public function requestClarification(Request $request, DamageReport $report): RedirectResponse
    {
        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final.');
        }

        $note = $request->input('clarification_note') ?? $request->input('identity_note');
        if (empty($note)) {
            return back()->withErrors(['clarification_note' => 'Catatan klarifikasi wajib diisi.']);
        }

        $report->update([
            'identity_status' => IdentityStatus::PERLU_KLARIFIKASI,
            'identity_note' => $note,
        ]);

        NotificationHelper::toUser(
            $report->reporter,
            'Klarifikasi Identitas Pengaduan',
            "Pengaduan {$report->report_code} memerlukan klarifikasi: {$note}",
            route('pengguna.damage-reports.show', $report),
            'help-circle',
            'yellow'
        );

        $this->logActivity('request_clarification', 'damage-reports', "Meminta klarifikasi identitas pada laporan: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Permintaan klarifikasi identitas berhasil disimpan.');
    }

    /**
     * Nyatakan laporan valid setelah pemeriksaan bukti & sarpras.
     */
    public function validateReport(Request $request, DamageReport $report): RedirectResponse
    {
        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final dan tidak dapat diubah validitasnya.');
        }

        $validated = $request->validate([
            'verification_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'validity_status' => ValidityStatus::VALID,
            'status' => $report->status === DamageReportStatus::MENUNGGU_VERIFIKASI ? DamageReportStatus::DIVERIFIKASI : $report->status,
            'verification_note' => $validated['verification_note'] ?? $report->verification_note,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
        ]);

        $this->logActivity('validate', 'damage-reports', "Menyatakan laporan valid: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan berhasil dinyatakan VALID. Silakan tentukan kategori kerusakan untuk proses persetujuan.');
    }

    /**
     * Alias verifikasi untuk kompatibilitas route sebelumnya.
     */
    public function verify(Request $request, DamageReport $report): RedirectResponse
    {
        return $this->validateReport($request, $report);
    }

    /**
     * Jalur A — Setujui & Tugaskan Kerusakan Ringan.
     * Cukup persetujuan Waka Sarpras (Level 1), tanpa diteruskan ke Kepala Sekolah.
     */
    public function approveMinor(Request $request, DamageReport $report): RedirectResponse
    {
        if ($report->identity_status !== IdentityStatus::TERVERIFIKASI) {
            return back()->with('error', 'Identitas pelapor harus terverifikasi sebelum persetujuan.');
        }

        if ($report->validity_status !== ValidityStatus::VALID) {
            return back()->with('error', 'Laporan harus dinyatakan valid sebelum persetujuan.');
        }

        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final.');
        }

        // Cegah persetujuan duplikat jika sudah ada proses repair
        if ($report->repairReports()->whereIn('status', ['ready', 'in_progress'])->exists()) {
            return back()->with('error', 'Laporan ini sudah memiliki proses perbaikan aktif.');
        }

        $validated = $request->validate([
            'category_note' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $validated) {
            $report->update([
                'damage_category' => DamageCategory::RINGAN,
                'category_note' => $validated['category_note'] ?? 'Kerusakan Ringan — ditangani langsung oleh Waka Sarpras',
                'status' => DamageReportStatus::DISETUJUI,
            ]);

            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 1],
                [
                    'approver_id' => auth()->id(),
                    'status' => ApprovalStatus::APPROVED,
                    'notes' => $validated['notes'] ?? 'Disetujui langsung oleh Waka Sarpras untuk perbaikan ringan.',
                    'approved_at' => now(),
                ]
            );
        });

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Kerusakan Disetujui',
            "Laporan {$report->report_code} (Kerusakan Ringan) telah disetujui Waka Sarpras dan siap diperbaiki.",
            route('pengguna.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        $this->logActivity('approve_minor', 'damage-reports', "Menyetujui kerusakan ringan: {$report->report_code}");

        return redirect()->route('waka.repair-reports.create', $report)
            ->with('success', 'Kerusakan ringan berhasil disetujui. Silakan input penugasan petugas untuk memulai perbaikan.');
    }

    /**
     * Jalur B — Ajukan Persetujuan Kerusakan Besar ke Kepala Sekolah.
     * Waka mencatat approval level 1 dan meneruskan laporan ke Kepala Sekolah untuk persetujuan kedua.
     */
    public function submitMajor(Request $request, DamageReport $report): RedirectResponse
    {
        if ($report->identity_status !== IdentityStatus::TERVERIFIKASI) {
            return back()->with('error', 'Identitas pelapor harus terverifikasi sebelum pengajuan ke Kepala Sekolah.');
        }

        if ($report->validity_status !== ValidityStatus::VALID) {
            return back()->with('error', 'Laporan harus dinyatakan valid sebelum pengajuan ke Kepala Sekolah.');
        }

        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final.');
        }

        $validated = $request->validate([
            'category_note' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $validated) {
            $report->update([
                'damage_category' => DamageCategory::BERAT,
                'category_note' => $validated['category_note'] ?? 'Kerusakan Besar — membutuhkan persetujuan Kepala Sekolah',
                'status' => DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            ]);

            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 1],
                [
                    'approver_id' => auth()->id(),
                    'status' => ApprovalStatus::APPROVED,
                    'notes' => $validated['notes'] ?? 'Diverifikasi Waka Sarpras dan diajukan ke Kepala Sekolah.',
                    'approved_at' => now(),
                ]
            );

            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 2],
                [
                    'approver_id' => auth()->id(), // Placeholder sebelum Kepsek menindaklanjuti
                    'status' => ApprovalStatus::PENDING,
                    'notes' => null,
                    'approved_at' => null,
                ]
            );
        });

        NotificationHelper::toRole(
            UserRole::KEPALA_SEKOLAH,
            'Pengajuan Persetujuan Kerusakan Besar',
            "Laporan {$report->report_code} ({$report->room->name}) memerlukan persetujuan Kepala Sekolah.",
            route('kepala.damage-reports.show', $report),
            'clock',
            'yellow'
        );

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Diajukan ke Kepala Sekolah',
            "Laporan {$report->report_code} (Kerusakan Besar) diteruskan ke Kepala Sekolah untuk persetujuan.",
            route('pengguna.damage-reports.show', $report),
            'clock',
            'yellow'
        );

        $this->logActivity('submit_major', 'damage-reports', "Mengajukan kerusakan besar ke Kepala Sekolah: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan kerusakan besar berhasil diajukan ke Kepala Sekolah untuk persetujuan.');
    }

    /**
     * Handler unified approve untuk form umum atau backwards compatibility.
     */
    public function approve(Request $request, DamageReport $report): RedirectResponse
    {
        $category = $request->input('damage_category');

        if ($category === DamageCategory::RINGAN->value || $category === 'ringan') {
            return $this->approveMinor($request, $report);
        }

        if ($category === DamageCategory::BERAT->value || $category === 'berat') {
            return $this->submitMajor($request, $report);
        }

        return back()->with('error', 'Kategori kerusakan harus dipilih: Kerusakan Ringan atau Kerusakan Besar.');
    }

    /**
     * Reject laporan (Waka).
     */
    public function reject(Request $request, DamageReport $report): RedirectResponse
    {
        if (in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK])) {
            return back()->with('error', 'Laporan sudah final dan tidak bisa ditolak lagi.');
        }

        $reason = $request->input('notes') ?? $request->input('rejection_reason');
        if (empty($reason)) {
            return back()->withErrors(['notes' => 'Alasan penolakan wajib diisi.']);
        }

        DB::transaction(function () use ($report, $reason) {
            $report->update([
                'status' => DamageReportStatus::DITOLAK,
                'validity_status' => ValidityStatus::DITOLAK,
                'rejection_reason' => $reason,
                'verification_note' => $reason,
                'verified_at' => $report->verified_at ?? now(),
                'verified_by' => $report->verified_by ?? auth()->id(),
            ]);

            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 1],
                [
                    'approver_id' => auth()->id(),
                    'status' => ApprovalStatus::REJECTED,
                    'notes' => $reason,
                    'approved_at' => now(),
                ]
            );
        });

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Ditolak',
            "Laporan {$report->report_code} ditolak: {$reason}",
            route('pengguna.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        $this->logActivity('reject', 'damage-reports', "Menolak laporan: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan berhasil ditolak.');
    }
}
