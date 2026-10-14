<?php

namespace App\Http\Controllers\Pengguna;

use App\Enums\UserRole;
use App\Helpers\NotificationHelper;
use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\Item;
use App\Models\Room;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    use LogsActivity;

    /**
     * Laporan saya.
     */
    public function index(Request $request): View
    {
        $reports = DamageReport::query()
            ->with(['room:id,name,code', 'itemRoom.item:id,name'])
            ->where('reporter_id', auth()->id())
            ->when($request->search, fn ($q, $s) => $q->where('report_code', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('pengguna.reports.index', [
            'reports'  => $reports,
            'statuses' => \App\Enums\DamageReportStatus::cases(),
        ]);
    }

    /**
     * Form buat laporan.
     */
    public function create(Request $request): View
    {
        $myRooms = Room::query()
            ->whereHas('roomUsers', fn ($q) => $q->where('user_id', auth()->id()))
            ->with(['itemRooms.item:id,name,code,photo'])
            ->orderBy('name')
            ->get();
        
        return view('pengguna.reports.create', [
            'myRooms'        => $myRooms,
            'selectedRoomId' => $request->room_id,
            'selectedItemRoomId' => $request->item_room_id,
        ]);
    }

    /**
     * Simpan laporan.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id'        => ['required', 'exists:rooms,id'],
            'item_room_id'   => ['nullable', 'exists:item_rooms,id'],
            'description'    => ['required', 'string', 'min:10', 'max:2000'],
            'evidence_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
        ]);

        $room = Room::findOrFail($validated['room_id']);
        abort_unless($room->isOwnedBy(auth()->id()), 403, 'Anda tidak memiliki akses ke ruangan ini.');

        if (!empty($validated['item_room_id'])) {
            $itemRoom = \App\Models\ItemRoom::findOrFail($validated['item_room_id']);
            abort_unless($itemRoom->room_id == $room->id, 422, 'Barang tidak berada di ruangan yang dipilih.');
        }

        $validated['reporter_id'] = auth()->id();
        $validated['report_code'] = DamageReport::generateReportCode();
        $validated['reported_at'] = now();
        $validated['status']      = 'menunggu_verifikasi';

        if ($request->hasFile('evidence_photo')) {
            $validated['evidence_photo'] = handle_upload($request->file('evidence_photo'), 'damage-reports');
        }

        $report = DamageReport::create($validated);

        $this->logActivity('create', 'damage-reports', "Membuat laporan kerusakan: {$report->report_code}");

        NotificationHelper::toRole(
            UserRole::WAKA_SARPRAS,
            'Laporan Kerusakan Baru',
            "Laporan {$report->report_code} dari {$report->room->name}",
            route('waka.damage-reports.show', $report),
            'alert-triangle',
            'yellow'
        );

        return redirect()
            ->route('pengguna.damage-reports.show', $report)
            ->with('success', 'Laporan berhasil dikirim. Menunggu verifikasi Waka Sarpras.');
    }

    public function show(DamageReport $report): View
    {
        abort_unless($report->reporter_id === auth()->id(), 403, 'Anda tidak memiliki akses ke laporan ini.');

        $report->load([
            'room:id,name,code,photo',
            'itemRoom.item:id,name,code',
            'approvals.approver:id,name',
            'repairReports',
        ]);

        return view('pengguna.reports.show', compact('report'));
    }

    /**
     * Hapus laporan (hanya jika masih menunggu verifikasi).
     */
    public function destroy(DamageReport $report): RedirectResponse
    {
        abort_unless($report->reporter_id === auth()->id(), 403);

        if ($report->status->value !== 'menunggu_verifikasi') {
            return back()->with('error', 'Laporan yang sudah diproses tidak dapat dihapus.');
        }

        if ($report->evidence_photo) {
            Storage::disk('public')->delete($report->evidence_photo);
        }

        $code = $report->report_code;
        $report->delete();

        $this->logActivity('delete', 'damage-reports', "Menghapus laporan: {$code}");

        return redirect()->route('pengguna.damage-reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}