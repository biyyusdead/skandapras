<?php

namespace App\Http\Controllers\Waka;

use App\Enums\DamageReportStatus;
use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\RepairReport;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RepairReportController extends Controller
{
    use LogsActivity;

    /**
     * Daftar repair report.
     */
    public function index(Request $request): View
    {
        $repairs = RepairReport::query()
            ->with(['damageReport:id,report_code,room_id', 'damageReport.room:id,name', 'creator:id,name'])
            ->when($request->search, fn ($q, $s) => $q->where('technician_name', 'like', "%{$s}%")
                ->orWhereHas('damageReport', fn ($q) => $q->where('report_code', 'like', "%{$s}%")))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'       => RepairReport::count(),
            'ready'       => RepairReport::where('status', 'ready')->count(),
            'in_progress' => RepairReport::where('status', 'in_progress')->count(),
            'completed'   => RepairReport::where('status', 'completed')->count(),
        ];

        return view('waka.repair-reports.index', [
            'repairs'  => $repairs,
            'statuses' => RepairStatus::cases(),
            'stats'    => $stats,
        ]);
    }

    /**
     * Form buat repair — dari damage report yang sudah disetujui 2 tahap.
     */
    public function create(DamageReport $report): View
    {
        // Validasi: laporan harus sudah approved kedua tahap
        abort_unless(
            $report->canBeRepaired(),
            403,
            'Laporan belum disetujui kedua tahap approval.'
        );

        // Cegah duplikasi kalau sudah ada repair aktif
        if ($report->repairReports()->whereIn('status', ['ready', 'in_progress'])->exists()) {
            return redirect()
                ->route('waka.damage-reports.show', $report)
                ->with('error', 'Laporan ini sudah memiliki proses perbaikan aktif.');
        }

        return view('waka.repair-reports.create', compact('report'));
    }

    /**
     * Simpan repair report.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'damage_report_id'        => ['required', 'exists:damage_reports,id'],
            'technician_name'         => ['required', 'string', 'max:255'],
            'technician_contact'      => ['nullable', 'string', 'max:255'],
            'repair_description'      => ['nullable', 'string', 'max:2000'],
            'repair_cost'             => ['nullable', 'numeric', 'min:0'],
            'started_at'              => ['nullable', 'date'],
            'repair_evidence_before'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes'                   => ['nullable', 'string', 'max:1000'],
        ]);

        $report = DamageReport::findOrFail($validated['damage_report_id']);

        // Validasi lagi
        abort_unless($report->canBeRepaired(), 403, 'Laporan belum disetujui kedua tahap.');

        $validated['created_by'] = auth()->id();
        $validated['status']     = RepairStatus::IN_PROGRESS;

        if ($request->hasFile('repair_evidence_before')) {
            $validated['repair_evidence_before'] = handle_upload($request->file('repair_evidence_before'), 'repairs');
        }

        $repair = RepairReport::create($validated);

        // Update status laporan → DIPROSES
        $report->update(['status' => DamageReportStatus::DIPROSES]);

        $this->logActivity('create', 'repair-reports', "Memulai perbaikan untuk laporan: {$report->report_code}");

        return redirect()
            ->route('waka.damage-reports.show', $report)
            ->with('success', 'Proses perbaikan berhasil dicatat.');
    }

    /**
     * Form edit.
     */
    public function edit(RepairReport $repair): View
    {
        $repair->load('damageReport.room');

        return view('waka.repair-reports.edit', compact('repair'));
    }

    /**
     * Update repair — termasuk tandai selesai.
     */
    public function update(Request $request, RepairReport $repair): RedirectResponse
    {
        $validated = $request->validate([
            'technician_name'        => ['required', 'string', 'max:255'],
            'technician_contact'     => ['nullable', 'string', 'max:255'],
            'repair_description'     => ['nullable', 'string', 'max:2000'],
            'repair_cost'            => ['nullable', 'numeric', 'min:0'],
            'started_at'             => ['nullable', 'date'],
            'completed_at'           => ['nullable', 'date'],
            'repair_evidence_before' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'repair_evidence_after'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'status'                 => ['required', 'in:ready,in_progress,completed'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('repair_evidence_before')) {
            $validated['repair_evidence_before'] = handle_upload(
                $request->file('repair_evidence_before'), 'repairs', $repair->repair_evidence_before
            );
        }

        if ($request->hasFile('repair_evidence_after')) {
            $validated['repair_evidence_after'] = handle_upload(
                $request->file('repair_evidence_after'), 'repairs', $repair->repair_evidence_after
            );
        }

        $repair->update($validated);

        // Kalau status = completed, update damage report → SELESAI
        if ($repair->status === RepairStatus::COMPLETED) {
            $repair->damageReport->update(['status' => DamageReportStatus::SELESAI]);

            // Update kondisi barang jika ada item
            if ($repair->damageReport->itemRoom) {
                $repair->damageReport->itemRoom->update(['condition' => 'baik']);
            }

            // Update kondisi ruangan jika sebelumnya rusak
            $room = $repair->damageReport->room;
            if ($room) {
                $room->update(['health_score' => $room->recalculateHealthScore()]);
            }

            NotificationHelper::toUser(
                $repair->damageReport->reporter,
                'Perbaikan Selesai',
                "Kerusakan di {$repair->damageReport->room->name} telah selesai diperbaiki",
                route('pengguna.damage-reports.show', $repair->damageReport),
                'check-check',
                'green'
            );

            // Notifikasi ke Kepala Sekolah
            NotificationHelper::toRole(
                UserRole::KEPALA_SEKOLAH,
                'Perbaikan Selesai',
                "Laporan {$repair->damageReport->report_code} telah selesai",
                route('kepala.damage-reports.show', $repair->damageReport),
                'check-check',
                'green'
            );
        }

        $this->logActivity('update', 'repair-reports', "Memperbarui perbaikan: {$repair->damageReport->report_code}");

        return redirect()
            ->route('waka.repair-reports.index')
            ->with('success', 'Data perbaikan berhasil diperbarui.');
    }

    public function destroy(RepairReport $repair): RedirectResponse
    {
        $code = $repair->damageReport->report_code;

        if ($repair->repair_evidence_before) Storage::disk('public')->delete($repair->repair_evidence_before);
        if ($repair->repair_evidence_after) Storage::disk('public')->delete($repair->repair_evidence_after);

        $repair->delete();

        // Kembalikan status damage report ke SIAP_DIPERBAIKI
        $repair->damageReport?->update(['status' => DamageReportStatus::SIAP_DIPERBAIKI]);

        $this->logActivity('delete', 'repair-reports', "Menghapus perbaikan: {$code}");

        return redirect()->route('waka.repair-reports.index')
            ->with('success', 'Data perbaikan berhasil dihapus.');
    }
}