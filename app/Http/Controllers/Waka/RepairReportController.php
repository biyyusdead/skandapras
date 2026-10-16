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
use Illuminate\Support\Facades\DB;
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

        $statsRow = RepairReport::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("COUNT(CASE WHEN status = 'ready' THEN 1 END) as ready")
            ->selectRaw("COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as in_progress")
            ->selectRaw("COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed")
            ->first();

        $stats = [
            'total' => (int) ($statsRow->total ?? 0),
            'ready' => (int) ($statsRow->ready ?? 0),
            'in_progress' => (int) ($statsRow->in_progress ?? 0),
            'completed' => (int) ($statsRow->completed ?? 0),
        ];

        return view('waka.repair-reports.index', [
            'repairs' => $repairs,
            'statuses' => RepairStatus::cases(),
            'stats' => $stats,
        ]);
    }

    /**
     * Form buat repair — dari damage report yang sudah disetujui 2 tahap.
     */
    public function create(DamageReport $report): View|RedirectResponse
    {
        // Validasi: laporan harus sudah disetujui sesuai kategori kerusakan
        abort_unless(
            $report->canBeRepaired(),
            403,
            'Laporan belum memenuhi syarat persetujuan perbaikan.'
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
            'damage_report_id' => ['required', 'exists:damage_reports,id'],
            'technician_name' => ['required', 'string', 'max:255'],
            'technician_contact' => ['nullable', 'string', 'max:255'],
            'repair_description' => ['nullable', 'string', 'max:2000'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'repair_evidence_before' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $report = DamageReport::findOrFail($validated['damage_report_id']);

        // Validasi syarat persetujuan
        if (! $report->canBeRepaired()) {
            return redirect()
                ->route('waka.damage-reports.show', $report)
                ->with('error', 'Laporan belum memenuhi syarat persetujuan untuk perbaikan.');
        }

        // Cegah duplikasi penugasan
        if ($report->repairReports()->whereIn('status', ['ready', 'in_progress'])->exists()) {
            return redirect()
                ->route('waka.damage-reports.show', $report)
                ->with('error', 'Laporan ini sudah memiliki proses perbaikan aktif.');
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = RepairStatus::IN_PROGRESS;

        if ($request->hasFile('repair_evidence_before')) {
            $validated['repair_evidence_before'] = handle_upload($request->file('repair_evidence_before'), 'repairs');
        }

        $repair = DB::transaction(function () use ($validated, $report) {
            $repair = RepairReport::create($validated);
            $report->update(['status' => DamageReportStatus::DIPROSES]);

            return $repair;
        });

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
            'technician_name' => ['required', 'string', 'max:255'],
            'technician_contact' => ['nullable', 'string', 'max:255'],
            'repair_description' => ['nullable', 'string', 'max:2000'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
            'repair_evidence_before' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'repair_evidence_after' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'status' => ['required', 'in:ready,in_progress,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // Otomatis ubah status menjadi Selesai jika foto bukti setelah diunggah atau tanggal selesai diisi
        if ($request->hasFile('repair_evidence_after') || ! empty($validated['completed_at'])) {
            $validated['status'] = RepairStatus::COMPLETED->value;
        }

        if ($validated['status'] === RepairStatus::COMPLETED->value && empty($validated['completed_at'])) {
            $validated['completed_at'] = now()->toDateString();
        }

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

        DB::transaction(function () use ($repair, $validated) {
            $repair->update($validated);

            if ($repair->status === RepairStatus::COMPLETED) {
                $this->markRepairAsCompleted($repair);
            }
        });

        $this->logActivity('update', 'repair-reports', "Memperbarui perbaikan: {$repair->damageReport->report_code}");

        return redirect()
            ->route('waka.repair-reports.index')
            ->with('success', 'Data perbaikan berhasil diperbarui.');
    }

    /**
     * Selesaikan perbaikan langsung dengan bukti foto dan tanggal penyelesaian.
     */
    public function complete(Request $request, RepairReport $repair): RedirectResponse
    {
        $validated = $request->validate([
            'repair_evidence_after' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'completed_at' => ['nullable', 'date'],
            'repair_cost' => ['nullable', 'numeric', 'min:0'],
            'repair_description' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($request->hasFile('repair_evidence_after')) {
            $validated['repair_evidence_after'] = handle_upload(
                $request->file('repair_evidence_after'), 'repairs', $repair->repair_evidence_after
            );
        }

        $validated['status'] = RepairStatus::COMPLETED->value;
        $validated['completed_at'] = $validated['completed_at'] ?? now()->toDateString();

        DB::transaction(function () use ($repair, $validated) {
            $repair->update($validated);
            $this->markRepairAsCompleted($repair);
        });

        $this->logActivity('update', 'repair-reports', "Menyelesaikan perbaikan untuk laporan: {$repair->damageReport->report_code}");

        return redirect()
            ->route('waka.damage-reports.show', $repair->damageReport)
            ->with('success', 'Perbaikan berhasil diselesaikan dan status laporan kini SELESAI.');
    }

    /**
     * Tandai laporan pengaduan, kondisi barang, dan ruangan selesai diperbaiki.
     */
    protected function markRepairAsCompleted(RepairReport $repair): void
    {
        $damageReport = $repair->damageReport;
        if (! $damageReport) {
            return;
        }

        $damageReport->update(['status' => DamageReportStatus::SELESAI]);

        // Update kondisi barang jika ada item terkait
        if ($damageReport->itemRoom) {
            $damageReport->itemRoom->update(['condition' => 'baik']);
        }

        // Hitung ulang kondisi ruangan
        $room = $damageReport->room;
        if ($room) {
            $room->update(['health_score' => $room->recalculateHealthScore()]);
        }

        NotificationHelper::toUser(
            $damageReport->reporter,
            'Perbaikan Selesai',
            "Kerusakan di {$damageReport->room?->name} telah selesai diperbaiki",
            route('pengguna.damage-reports.show', $damageReport),
            'check-check',
            'green'
        );

        // Notifikasi ke Kepala Sekolah
        NotificationHelper::toRole(
            UserRole::KEPALA_SEKOLAH,
            'Perbaikan Selesai',
            "Laporan {$damageReport->report_code} telah selesai diperbaiki",
            route('kepala.damage-reports.show', $damageReport),
            'check-check',
            'green'
        );
    }

    public function destroy(RepairReport $repair): RedirectResponse
    {
        $code = $repair->damageReport->report_code;

        if ($repair->repair_evidence_before) {
            Storage::disk('public')->delete($repair->repair_evidence_before);
        }
        if ($repair->repair_evidence_after) {
            Storage::disk('public')->delete($repair->repair_evidence_after);
        }

        $repair->delete();

        // Kembalikan status damage report ke SIAP_DIPERBAIKI
        $repair->damageReport?->update(['status' => DamageReportStatus::SIAP_DIPERBAIKI]);

        $this->logActivity('delete', 'repair-reports', "Menghapus perbaikan: {$code}");

        return redirect()->route('waka.repair-reports.index')
            ->with('success', 'Data perbaikan berhasil dihapus.');
    }
}
