<?php

namespace App\Http\Controllers\Waka;

use App\Enums\RoomCondition;
use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    use LogsActivity, BulkActionable;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Room::class,
            'rooms',
            ['delete_files' => ['photo', 'panorama_file']]
        );
    }

    public function index(Request $request): View
    {
        $rooms = Room::query()
            ->withCount(['items', 'roomUsers'])
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            }))
            ->when($request->condition, fn ($q, $c) => $q->where('condition', $c))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('waka.rooms.index', [
            'rooms'      => $rooms,
            'conditions' => RoomCondition::cases(),
        ]);
    }

    public function create(): View
    {
        return view('waka.rooms.create', [
            'conditions' => RoomCondition::cases(),
            'users'      => User::whereIn('role', ['pengguna_ruangan'])->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRoom($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'rooms');
        }

        if ($request->hasFile('panorama_file')) {
            $validated['panorama_file'] = handle_upload($request->file('panorama_file'), 'panorama');
        }

        $room = Room::create($validated);

        // Assign pengguna ruangan
        if ($request->filled('users')) {
            $room->users()->syncWithPivotValues($request->users, ['assigned_at' => now()]);
        }

        // Hitung health score awal
        $room->update(['health_score' => $room->recalculateHealthScore()]);

        $this->logActivity('create', 'rooms', "Menambahkan ruangan: {$room->name}");

        return redirect()->route('waka.rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function show(Room $room): View
    {
        $room->load([
            'itemRooms.item.category',
            'roomUsers.user',
            'damageReports' => fn ($q) => $q->with('reporter:id,name')->latest()->take(10),
        ]);

        return view('waka.rooms.show', compact('room'));
    }

    public function edit(Room $room): View
    {
        return view('waka.rooms.edit', [
            'room'       => $room->load('roomUsers'),
            'conditions' => RoomCondition::cases(),
            'users'      => User::whereIn('role', ['pengguna_ruangan'])->active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $this->validateRoom($request, $room->id);

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'rooms', $room->photo);
        }

        if ($request->hasFile('panorama_file')) {
            $validated['panorama_file'] = handle_upload($request->file('panorama_file'), 'panorama', $room->panorama_file);
        }

        $room->update($validated);

        // Sync pengguna ruangan
        if ($request->has('users')) {
            $room->users()->syncWithPivotValues($request->users ?? [], ['assigned_at' => now()]);
        }

        // Recalculate health
        $room->update(['health_score' => $room->recalculateHealthScore()]);

        $this->logActivity('update', 'rooms', "Memperbarui ruangan: {$room->name}");

        return redirect()->route('waka.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $name = $room->name;

        if ($room->photo) Storage::disk('public')->delete($room->photo);
        if ($room->panorama_file) Storage::disk('public')->delete($room->panorama_file);
        $room->delete();

        $this->logActivity('delete', 'rooms', "Menghapus ruangan: {$name}");

        return redirect()->route('waka.rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    protected function validateRoom(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'code'         => ['required', 'string', 'max:30', Rule::unique('rooms', 'code')->ignore($ignoreId)],
            'floor'        => ['nullable', 'string', 'max:20'],
            'capacity'     => ['nullable', 'integer', 'min:0'],
            'description'  => ['nullable', 'string'],
            'condition'    => ['required', Rule::enum(RoomCondition::class)],
            'panorama_url' => ['nullable', 'string', 'max:1000'],
            'panorama_file'=> ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'is_active'    => ['boolean'],
            'users'        => ['nullable', 'array'],
            'users.*'      => ['exists:users,id'],

            // Peta 2D
            'map_x'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'map_y'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'map_floor' => ['nullable', 'string', 'max:10'],

            // Model 3D
            'model_3d_x'       => ['nullable', 'numeric', 'min:0', 'max:2000'],
            'model_3d_y'       => ['nullable', 'numeric', 'min:0', 'max:1200'],
            'model_3d_w'       => ['nullable', 'numeric', 'min:1', 'max:500'],
            'model_3d_h'       => ['nullable', 'numeric', 'min:1', 'max:500'],
            'model_3d_height'  => ['nullable', 'numeric', 'min:0.5', 'max:50'],
            'model_3d_shape'   => ['nullable', Rule::in(['pitched', 'flat', 'dome', 'cylinder'])],
            'model_3d_color'   => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'model_3d_visible' => ['boolean'],
        ]);

        $validated['is_active']        = $request->boolean('is_active');
        $validated['model_3d_visible'] = $request->boolean('model_3d_visible');

        // Default untuk kolom 3D kalau user isi X & Y tapi W/H/Height kosong
        if (!empty($validated['model_3d_x']) && !empty($validated['model_3d_y'])) {
            $validated['model_3d_w']      = $validated['model_3d_w']      ?? 90;
            $validated['model_3d_h']      = $validated['model_3d_h']      ?? 52;
            $validated['model_3d_height'] = $validated['model_3d_height'] ?? 3;
            $validated['model_3d_shape']  = $validated['model_3d_shape']  ?? 'pitched';
            $validated['model_3d_color']  = $validated['model_3d_color']  ?? '#7fa8ee';
        }

        return $validated;
    }

    public function deletePanorama(Room $room): RedirectResponse
    {
        if ($room->panorama_file) {
            Storage::disk('public')->delete($room->panorama_file);
            $room->update(['panorama_file' => null]);

            $this->logActivity('delete', 'rooms', "Menghapus file panorama ruangan: {$room->name}");

            return back()->with('success', 'File panorama berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada file panorama untuk dihapus.');
    }
}