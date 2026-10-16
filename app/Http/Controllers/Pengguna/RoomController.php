<?php

namespace App\Http\Controllers\Pengguna;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Daftar ruangan yang menjadi tanggung jawab user.
     */
    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->whereHas('roomUsers', fn ($q) => $q->where('user_id', auth()->id()))
            ->withCount(['items', 'damageReports'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"))
            ->orderBy('name')
            ->get();

        return view('pengguna.rooms.index', compact('rooms'));
    }

    /**
     * Detail ruangan — dengan proteksi ownership.
     */
    public function show(Room $room): View
    {
        // Proteksi: hanya boleh lihat ruangan miliknya
        abort_unless(
            $room->isOwnedBy(auth()->id()),
            403,
            'Anda tidak memiliki akses ke ruangan ini.'
        );

        $room->load([
            'itemRooms.item.category',
            'damageReports' => fn ($q) => $q->with('reporter:id,name')->latest()->take(10),
        ]);

        return view('pengguna.rooms.show', compact('room'));
    }
}
