<?php

namespace App\Http\Controllers\Waka;

use App\Enums\ApprovalStatus;
use App\Enums\DamageReportStatus;
use App\Enums\UserRole;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\DamageReport;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $stats = [
            'total'      => DamageReport::count(),
            'menunggu'   => DamageReport::where('status', 'menunggu_verifikasi')->count(),
            'diproses'   => DamageReport::whereIn('status', ['disetujui', 'siap_diperbaiki', 'diproses'])->count(),
            'selesai'    => DamageReport::where('status', 'selesai')->count(),
        ];

        return view('waka.damage-reports.index', [
            'reports'   => $reports,
            'statuses'  => DamageReportStatus::cases(),
            'stats'     => $stats,
            'rooms'     => \App\Models\Room::orderBy('name')->get(['id', 'name']),
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
     * Verifikasi laporan (tanpa approval).
     * Mengubah status dari menunggu_verifikasi → diverifikasi.
     */
    public function verify(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless($report->status === DamageReportStatus::MENUNGGU_VERIFIKASI, 422, 'Laporan sudah diverifikasi.');

        $validated = $request->validate([
            'verification_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $report->update([
            'status'           => DamageReportStatus::DIVERIFIKASI,
            'verification_note'=> $validated['verification_note'] ?? null,
            'verified_at'      => now(),
            'verified_by'      => auth()->id(),
        ]);

        $this->logActivity('verify', 'damage-reports', "Memverifikasi laporan: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan berhasil diverifikasi.');
    }

    /**
     * Approval Tahap 1 (Waka Sarpras).
     * Setelah ini status → menunggu_persetujuan_2 (ke Kepala Sekolah).
     */
    public function approve(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless(
            in_array($report->status, [DamageReportStatus::DIVERIFIKASI, DamageReportStatus::MENUNGGU_PERSETUJUAN_1]),
            422,
            'Laporan belum siap untuk disetujui.'
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update status
        $report->update([
            'status' => DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
        ]);

        Approval::updateOrCreate(
            ['damage_report_id' => $report->id, 'approval_level' => 1],
            [
                'approver_id' => auth()->id(),
                'status'      => ApprovalStatus::APPROVED,
                'notes'       => $validated['notes'] ?? null,
                'approved_at' => now(),
            ]
        );

        NotificationHelper::toRole(
            UserRole::KEPALA_SEKOLAH,
            'Persetujuan Tahap 2 Diperlukan',
            "Laporan {$report->report_code} dari {$report->room->name} menunggu persetujuan Anda",
            route('kepala.damage-reports.show', $report),
            'clock',
            'yellow'
        );

        // Notifikasi ke Pelapor
        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Anda Disetujui Waka',
            "Laporan {$report->report_code} diteruskan ke Kepala Sekolah",
            route('pengguna.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        // Buat placeholder approval level 2 (pending)
        Approval::firstOrCreate(
            ['damage_report_id' => $report->id, 'approval_level' => 2],
            ['approver_id' => auth()->id(), 'status' => ApprovalStatus::PENDING]
        );

        $this->logActivity('approve', 'damage-reports', "Menyetujui laporan tahap 1: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan disetujui. Menunggu persetujuan Kepala Sekolah.');
    }

    /**
     * Reject laporan (Waka).
     */
    public function reject(Request $request, DamageReport $report): RedirectResponse
    {
        abort_if(
            in_array($report->status, [DamageReportStatus::SELESAI, DamageReportStatus::DITOLAK]),
            422,
            'Laporan sudah final.'
        );

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $report->update([
            'status'           => DamageReportStatus::DITOLAK,
            'verification_note'=> $validated['notes'],
            'verified_at'      => $report->verified_at ?? now(),
            'verified_by'      => $report->verified_by ?? auth()->id(),
        ]);

        // Simpan approval level 1 rejected
        Approval::updateOrCreate(
            ['damage_report_id' => $report->id, 'approval_level' => 1],
            [
                'approver_id' => auth()->id(),
                'status'      => ApprovalStatus::REJECTED,
                'notes'       => $validated['notes'],
                'approved_at' => now(),
            ]
        );

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Ditolak',
            "Laporan {$report->report_code} ditolak: {$validated['notes']}",
            route('pengguna.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        $this->logActivity('reject', 'damage-reports', "Menolak laporan: {$report->report_code}");

        return redirect()->route('waka.damage-reports.show', $report)
            ->with('success', 'Laporan ditolak.');
    }
}