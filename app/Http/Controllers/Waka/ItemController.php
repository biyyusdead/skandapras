<?php

namespace App\Http\Controllers\Waka;

use App\Enums\ItemCondition;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Room;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Item::class,
            'items',
            ['delete_files' => ['photo']]
        );
    }

    public function index(Request $request): View
    {
        $items = Item::query()
            ->with(['category:id,name', 'itemRooms.room:id,name,code'])
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('brand', 'like', "%{$s}%");
            }))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->room_id, fn ($q, $r) => $q->whereHas('itemRooms',
                fn ($q) => $q->where('room_id', $r)))
            ->when($request->condition, fn ($q, $c) => $q->whereHas('itemRooms',
                fn ($q) => $q->where('condition', $c)))
            ->when($request->status !== null && $request->status !== '',
                fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('waka.items.index', [
            'items' => $items,
            'rooms' => Room::active()->orderBy('name')->get(['id', 'name', 'code']),
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'conditions' => ItemCondition::cases(),
        ]);
    }

    public function create(): View
    {
        return view('waka.items.create', [
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'rooms' => Room::active()->orderBy('name')->get(['id', 'name', 'code']),
            'conditions' => ItemCondition::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateItem($request);

        DB::transaction(function () use ($validated, $request) {
            $itemData = collect($validated)->only([
                'category_id', 'name', 'code', 'brand', 'description', 'is_active',
            ])->toArray();

            $itemData['is_active'] = $request->boolean('is_active');

            if ($request->hasFile('photo')) {
                $itemData['photo'] = handle_upload($request->file('photo'), 'items');
            }

            $item = Item::create($itemData);

            $this->syncItemRooms($item, $validated['rooms'] ?? []);
            $this->recalculateRooms(array_column($validated['rooms'] ?? [], 'room_id'));

            $this->logActivity('create', 'items', "Menambahkan barang: {$item->name}");
        });

        return redirect()->route('waka.items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Request $request, Item $item): View
    {
        $item->load(['category:id,name']);

        $search = trim((string) $request->input('room_q', ''));

        $itemRooms = $item->itemRooms()
            ->with('room:id,name,code')
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('room', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate(5, ['*'], 'dist_page')
            ->withQueryString();

        return view('waka.items.edit', [
            'item' => $item,
            'itemRooms' => $itemRooms,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'rooms' => Room::active()->orderBy('name')->get(['id', 'name', 'code']),
            'conditions' => ItemCondition::cases(),
            'search' => $search,
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $this->validateItem($request, $item->id);

        DB::transaction(function () use ($validated, $request, $item) {
            $itemData = collect($validated)->only([
                'category_id', 'name', 'code', 'brand', 'description', 'is_active',
            ])->toArray();

            $itemData['is_active'] = $request->boolean('is_active');

            if ($request->hasFile('photo')) {
                $itemData['photo'] = handle_upload($request->file('photo'), 'items', $item->photo);
            }

            $item->update($itemData);

            $oldRooms = $item->itemRooms()->pluck('room_id')->toArray();

            $this->syncItemRooms($item, $validated['rooms'] ?? []);

            $affected = array_unique(array_merge(
                $oldRooms,
                array_column($validated['rooms'] ?? [], 'room_id')
            ));
            $this->recalculateRooms($affected);

            $this->logActivity('update', 'items', "Memperbarui barang: {$item->name}");
        });

        return redirect()->route('waka.items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        $name = $item->name;
        $roomIds = $item->itemRooms()->pluck('room_id')->toArray();

        DB::transaction(function () use ($item, $roomIds, $name) {
            if ($item->photo) {
                Storage::disk('public')->delete($item->photo);
            }

            $item->delete();

            $this->recalculateRooms($roomIds);
            $this->logActivity('delete', 'items', "Menghapus barang: {$name}");
        });

        return redirect()->route('waka.items.index')->with('success', 'Barang berhasil dihapus.');
    }

    // ============================================================
    // HELPERS
    // ============================================================

    protected function validateItem(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50',
                Rule::unique('items', 'code')->ignore($ignoreId)],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_active' => ['boolean'],

            'rooms' => ['required', 'array', 'min:1'],
            'rooms.*.room_id' => ['required', 'exists:rooms,id', 'distinct'],
            'rooms.*.quantity' => ['required', 'integer', 'min:1'],
            'rooms.*.condition' => ['required', Rule::enum(ItemCondition::class)],
            'rooms.*.notes' => ['nullable', 'string', 'max:500'],
        ], [
            'rooms.required' => 'Minimal 1 ruangan harus diisi.',
            'rooms.min' => 'Minimal 1 ruangan harus diisi.',
        ]);
    }

    protected function syncItemRooms(Item $item, array $rooms): void
    {
        $incomingRoomIds = array_column($rooms, 'room_id');

        $item->itemRooms()->whereNotIn('room_id', $incomingRoomIds)->delete();

        foreach ($rooms as $row) {
            $item->itemRooms()->updateOrCreate(
                ['room_id' => $row['room_id']],
                [
                    'quantity' => $row['quantity'],
                    'condition' => $row['condition'],
                    'notes' => $row['notes'] ?? null,
                ]
            );
        }
    }

    protected function recalculateRooms(array $roomIds): void
    {
        if (empty($roomIds)) {
            return;
        }

        Room::whereIn('id', $roomIds)->get()->each(function ($room) {
            $room->update(['health_score' => $room->recalculateHealthScore()]);
        });
    }
}
