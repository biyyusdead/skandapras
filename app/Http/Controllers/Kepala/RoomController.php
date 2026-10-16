<?php

namespace App\Http\Controllers\Kepala;

use App\Enums\RoomCondition;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->withCount(['items', 'roomUsers'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"))
            ->when($request->condition, fn ($q, $c) => $q->where('condition', $c))
            ->orderBy('health_score')
            ->paginate(12)
            ->withQueryString();

        // Stat
        $stats = [
            'total' => Room::count(),
            'baik' => Room::where('condition', 'baik')->count(),
            'rusak_ringan' => Room::where('condition', 'rusak_ringan')->count(),
            'rusak_berat' => Room::where('condition', 'rusak_berat')->count(),
            'avg_score' => round(Room::avg('health_score') ?? 0),
        ];

        return view('kepala.rooms.index', [
            'rooms' => $rooms,
            'stats' => $stats,
            'conditions' => RoomCondition::cases(),
        ]);
    }

    public function show(Room $room): View
    {
        $room->load([
            'itemRooms.item.category',
            'roomUsers.user',
            'damageReports' => fn ($q) => $q->with('reporter:id,name')->latest()->take(10),
        ]);

        return view('kepala.rooms.show', compact('room'));
    }
}
