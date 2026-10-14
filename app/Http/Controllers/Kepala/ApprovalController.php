<?php

namespace App\Http\Controllers\Kepala;

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

class ApprovalController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        // Laporan yang menunggu persetujuan tahap 2
        $pending = DamageReport::query()
            ->with(['room:id,name', 'reporter:id,name'])
            ->where('status', 'menunggu_persetujuan_2')
            ->latest()
            ->paginate(15);

        // Riwayat approval kepsek
        $history = Approval::query()
            ->with(['damageReport:id,report_code,room_id', 'damageReport.room:id,name'])
            ->where('approval_level', 2)
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(15, ['*'], 'history_page');

        return view('kepala.approvals.index', compact('pending', 'history'));
    }

    /**
     * Approve Tahap 2 (Kepala Sekolah).
     * Setelah ini status → disetujui/siap_diperbaiki.
     */
    public function approve(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless(
            $report->status === DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            422,
            'Laporan tidak dalam status menunggu persetujuan 2.'
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Simpan/buat approval level 2
        Approval::updateOrCreate(
            ['damage_report_id' => $report->id, 'approval_level' => 2],
            [
                'approver_id' => auth()->id(),
                'status'      => ApprovalStatus::APPROVED,
                'notes'       => $validated['notes'] ?? null,
                'approved_at' => now(),
            ]
        );

        // Notifikasi ke Waka
        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Siap Diperbaiki',
            "Laporan {$report->report_code} telah disetujui Kepala Sekolah",
            route('waka.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        // Notifikasi ke Pelapor
        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Anda Disetujui',
            "Laporan {$report->report_code} akan segera diproses",
            route('pengguna.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        // Update status → disetujui
        $report->update(['status' => DamageReportStatus::DISETUJUI]);

        $this->logActivity('approve', 'damage-reports', "Menyetujui laporan tahap 2: {$report->report_code}");

        return redirect()->route('kepala.damage-reports.show', $report)
            ->with('success', 'Laporan disetujui. Waka Sarpras dapat memulai perbaikan.');
    }

    /**
     * Reject Tahap 2 (Kepala Sekolah).
     */
    public function reject(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless(
            $report->status === DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            422,
            'Laporan tidak dalam status menunggu persetujuan 2.'
        );

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        Approval::updateOrCreate(
            ['damage_report_id' => $report->id, 'approval_level' => 2],
            [
                'approver_id' => auth()->id(),
                'status'      => ApprovalStatus::REJECTED,
                'notes'       => $validated['notes'],
                'approved_at' => now(),
            ]
        );

        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Ditolak Kepala Sekolah',
            "Laporan {$report->report_code} ditolak: {$validated['notes']}",
            route('waka.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Ditolak',
            "Laporan {$report->report_code} ditolak oleh Kepala Sekolah",
            route('pengguna.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        $report->update(['status' => DamageReportStatus::DITOLAK]);

        $this->logActivity('reject', 'damage-reports', "Menolak laporan tahap 2: {$report->report_code}");

        return redirect()->route('kepala.damage-reports.show', $report)
            ->with('success', 'Laporan ditolak.');
    }
}