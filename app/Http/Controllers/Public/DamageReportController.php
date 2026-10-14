<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use App\Models\ItemRoom;
use App\Models\Room;
use App\Notifications\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    public function create(): View
    {
        $rooms = Room::active()->with(['itemRooms.item:id,name,code'])->orderBy('name')->get();

        return view('public.sarpras.pengaduan', compact('rooms'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reporter_name'  => ['required', 'string', 'max:255'],
            'room_id'        => ['required', 'exists:rooms,id'],
            'item_room_id'   => ['nullable', 'exists:item_rooms,id'],
            'description'    => ['required', 'string', 'min:10', 'max:2000'],
            'evidence_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        // Cek item_room kalau dipilih
        if (!empty($validated['item_room_id'])) {
            $itemRoom = ItemRoom::findOrFail($validated['item_room_id']);
            if ($itemRoom->room_id != $validated['room_id']) {
                return back()->withErrors(['item_room_id' => 'Barang tidak berada di ruangan yang dipilih.'])->withInput();
            }
        }

        // Simpan foto
        if ($request->hasFile('evidence_photo')) {
            $validated['evidence_photo'] = $request->file('evidence_photo')->store('damage-reports', 'public');
        }

        // Buat laporan
        $report = DamageReport::create([
            'report_code'      => DamageReport::generateReportCode(),
            'room_id'          => $validated['room_id'],
            'item_room_id'     => $validated['item_room_id'] ?? null,
            'reporter_id'      => auth()->id() ?? null,
            'description' => $validated['description'] . "\n\n— Pelapor: " . $validated['reporter_name'] . " (Pengunjung)",
            'evidence_photo'   => $validated['evidence_photo'] ?? null,
            'reported_at'      => now(),
            'status'           => 'menunggu_verifikasi',
        ]);

        // Notifikasi ke Waka Sarpras
        $wakaUsers = \App\Models\User::where('role', 'waka_sarpras')->where('is_active', true)->get();
        foreach ($wakaUsers as $waka) {
            $waka->notify(new AppNotification(
                'Laporan Kerusakan Baru',
                "Laporan {$report->report_code} dari {$report->room->name}",
                route('waka.damage-reports.show', $report),
                'alert-triangle',
                'yellow'
            ));
        }

        return redirect()
            ->route('public.rooms.show', $report->room->code)
            ->with('success', 'Laporan berhasil dikirim. Tim Sarpras akan menindaklanjuti.');
    }
}