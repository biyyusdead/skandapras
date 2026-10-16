<?php

namespace App\Http\Controllers\Kepala;

use App\Enums\ApprovalStatus;
use App\Enums\DamageCategory;
use App\Enums\DamageReportStatus;
use App\Enums\UserRole;
use App\Enums\ValidityStatus;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\DamageReport;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    use LogsActivity;

    public function index(Request $request): View
    {
        // Hanya laporan kerusakan besar yang valid dan diajukan Waka yang masuk ke Kepala Sekolah
        $pending = DamageReport::query()
            ->with(['room:id,name', 'reporter:id,name'])
            ->where('status', DamageReportStatus::MENUNGGU_PERSETUJUAN_2->value)
            ->where(function ($q) {
                $q->where('damage_category', DamageCategory::BERAT->value)
                    ->orWhereNull('damage_category'); // fallback legacy
            })
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
     * Setelah ini status → disetujui / siap ditugaskan untuk perbaikan.
     */
    public function approve(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless(
            $report->status === DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            422,
            'Laporan tidak dalam status menunggu persetujuan Kepala Sekolah.'
        );

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($report, $validated) {
            // Simpan/buat approval level 2
            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 2],
                [
                    'approver_id' => auth()->id(),
                    'status' => ApprovalStatus::APPROVED,
                    'notes' => $validated['notes'] ?? null,
                    'approved_at' => now(),
                ]
            );

            // Update status laporan → disetujui
            $report->update([
                'status' => DamageReportStatus::DISETUJUI,
            ]);
        });

        // Notifikasi ke Waka Sarpras
        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Kerusakan Besar Disetujui',
            "Laporan {$report->report_code} telah disetujui Kepala Sekolah dan siap ditugaskan perbaikan.",
            route('waka.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        // Notifikasi ke Pelapor
        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Anda Disetujui Kepala Sekolah',
            "Laporan {$report->report_code} telah disetujui Kepala Sekolah dan akan segera diproses perbaikan.",
            route('pengguna.damage-reports.show', $report),
            'check-circle',
            'green'
        );

        $this->logActivity('approve', 'damage-reports', "Menyetujui laporan tahap 2: {$report->report_code}");

        return redirect()->route('kepala.damage-reports.show', $report)
            ->with('success', 'Laporan kerusakan besar berhasil disetujui. Waka Sarpras dapat menugaskan petugas perbaikan.');
    }

    /**
     * Reject Tahap 2 (Kepala Sekolah).
     */
    public function reject(Request $request, DamageReport $report): RedirectResponse
    {
        abort_unless(
            $report->status === DamageReportStatus::MENUNGGU_PERSETUJUAN_2,
            422,
            'Laporan tidak dalam status menunggu persetujuan Kepala Sekolah.'
        );

        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        DB::transaction(function () use ($report, $validated) {
            Approval::updateOrCreate(
                ['damage_report_id' => $report->id, 'approval_level' => 2],
                [
                    'approver_id' => auth()->id(),
                    'status' => ApprovalStatus::REJECTED,
                    'notes' => $validated['notes'],
                    'approved_at' => now(),
                ]
            );

            $report->update([
                'status' => DamageReportStatus::DITOLAK,
                'validity_status' => ValidityStatus::DITOLAK,
                'rejection_reason' => $validated['notes'],
            ]);
        });

        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Ditolak Kepala Sekolah',
            "Laporan {$report->report_code} ditolak Kepala Sekolah: {$validated['notes']}",
            route('waka.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        NotificationHelper::toUser(
            $report->reporter,
            'Laporan Ditolak',
            "Laporan {$report->report_code} ditolak oleh Kepala Sekolah: {$validated['notes']}",
            route('pengguna.damage-reports.show', $report),
            'x-circle',
            'red'
        );

        $this->logActivity('reject', 'damage-reports', "Menolak laporan tahap 2: {$report->report_code}");

        return redirect()->route('kepala.damage-reports.show', $report)
            ->with('success', 'Laporan berhasil ditolak.');
    }
}
