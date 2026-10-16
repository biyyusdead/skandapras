<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\ItemRoom;
use App\Models\Room;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    public function create(): View
    {
        $rooms = Room::active()
            ->with(['itemRooms.item:id,name,code'])
            ->orderBy('name')
            ->get();

        // Transform data untuk Alpine (di controller, bukan di Blade)
        $roomsData = $rooms->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->name,
                'items' => $r->itemRooms->map(function ($ir) {
                    return [
                        'id' => $ir->id,
                        'name' => $ir->item->name,
                        'quantity' => $ir->quantity,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return view('public.sarpras.pengaduan', compact('rooms', 'roomsData'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'reporter_name' => ['required', 'string', 'max:255'],
            'room_id' => ['required', 'exists:rooms,id'],
            'item_room_id' => ['nullable', 'exists:item_rooms,id'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'evidence_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Cek item_room kalau dipilih
        if (! empty($validated['item_room_id'])) {
            $itemRoom = ItemRoom::findOrFail($validated['item_room_id']);
            if ($itemRoom->room_id != $validated['room_id']) {
                $msg = 'Barang tidak berada di ruangan yang dipilih.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $msg,
                        'errors' => ['item_room_id' => [$msg]],
                    ], 422);
                }

                return back()->withErrors(['item_room_id' => $msg])->withInput();
            }
        }

        // Simpan foto
        if ($request->hasFile('evidence_photo')) {
            $validated['evidence_photo'] = $request->file('evidence_photo')
                ->store('damage-reports', 'public');
        }

        // Buat laporan
        $report = DamageReport::create([
            'report_code' => DamageReport::generateReportCode(),
            'room_id' => $validated['room_id'],
            'item_room_id' => $validated['item_room_id'] ?? null,
            'reporter_id' => auth()->id() ?? null,
            'description' => $validated['description']
                                ."\n\n— Pelapor: "
                                .$validated['reporter_name']
                                .' (Pengunjung)',
            'evidence_photo' => $validated['evidence_photo'] ?? null,
            'reported_at' => now(),
            'status' => 'menunggu_verifikasi',
        ]);

        // Notifikasi ke Waka Sarpras
        $wakaUsers = User::where('role', 'waka_sarpras')
            ->where('is_active', true)
            ->get();

        foreach ($wakaUsers as $waka) {
            $waka->notify(new AppNotification(
                'Laporan Kerusakan Baru',
                "Laporan {$report->report_code} dari {$report->room->name}",
                route('waka.damage-reports.show', $report),
                'alert-triangle',
                'yellow'
            ));
        }

        // ============================================================
        // RESPONSE — AJAX vs normal request
        // ============================================================
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim. Tim Sarpras akan menindaklanjuti.',
                'report_code' => $report->report_code,
            ]);
        }

        return redirect()
            ->route('public.rooms.show', $report->room->code)
            ->with('success', 'Laporan berhasil dikirim. Tim Sarpras akan menindaklanjuti.');
    }
}
