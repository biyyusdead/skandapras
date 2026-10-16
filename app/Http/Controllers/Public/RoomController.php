<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->active()
            ->withCount('itemRooms')
            ->when($request->search, fn ($q, $s) => $q->where(function ($sub) use ($s) {
                $sub->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%");
            }))
            ->when($request->condition, fn ($q, $c) => $q->where('condition', $c))
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        return view('public.sarpras.index', compact('rooms'));
    }

    public function show(Request $request, Room $room): View
    {
        abort_unless($room->is_active, 404);

        $room->load('items.category');

        $search = trim((string) $request->input('fas_q', ''));
        $perPage = 6;

        $items = $room->itemRooms()
            ->with('item:id,name,photo')
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('item', fn ($qq) => $qq->where('name', 'like', "%{$search}%"));
            })
            ->paginate($perPage, ['*'], 'fas_page')
            ->withQueryString();

        return view('public.sarpras.show', compact('room', 'items', 'search'));
    }

    public function map(Request $request): View
    {
        $floor = $request->input('floor');
        $condition = $request->input('condition');

        // Marker 2D — tetap filter by map_x/map_y (untuk ditampilkan di denah)
        $rooms = Room::query()
            ->active()
            ->whereNotNull('map_x')
            ->whereNotNull('map_y')
            ->when($floor, fn ($q, $f) => $q->where('map_floor', $f))
            ->when($condition, fn ($q, $c) => $q->where('condition', $c))
            ->orderBy('map_y')
            ->orderBy('map_x')
            ->get();

        // ⬇️ Data untuk JS — SEMUA ruangan aktif, bukan cuma yang punya marker
        $roomsForJs = Room::active()
            ->select(['id', 'name', 'code', 'condition', 'map_floor', 'floor', 'description', 'capacity', 'health_score', 'photo'])
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name,
                'code' => $r->code,
                'condition' => $r->condition->value,
                'conditionLabel' => $r->condition->label(),
                'floor' => $r->map_floor ?? $r->floor,
                'description' => $r->description,
                'capacity' => $r->capacity,
                'health_score' => $r->health_score,
                'photo_url' => $r->photo ? asset('storage/'.$r->photo) : null,
                'detail_url' => route('public.rooms.show', $r->code),
                'report_url' => route('public.sarpras.pengaduan', ['room_id' => $r->id]),
            ])->values()->all();

        $floors = Room::active()
            ->whereNotNull('map_floor')
            ->distinct()
            ->orderBy('map_floor')
            ->pluck('map_floor');

        $stats = Room::where('is_active', 1)
            ->whereNotNull('map_x')
            ->whereNotNull('map_y')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN `condition` = 'baik' THEN 1 ELSE 0 END) as baik")
            ->selectRaw("SUM(CASE WHEN `condition` = 'rusak_ringan' THEN 1 ELSE 0 END) as rusak_ringan")
            ->selectRaw("SUM(CASE WHEN `condition` = 'rusak_berat' THEN 1 ELSE 0 END) as rusak_berat")
            ->first();

        return view('public.sarpras.peta', compact('rooms', 'roomsForJs', 'floors', 'floor', 'condition', 'stats'));
    }
}
