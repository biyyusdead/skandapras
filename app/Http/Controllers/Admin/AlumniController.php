<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use App\Models\Department;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlumniController extends Controller
{
    use LogsActivity, BulkActionable;

    public function index(Request $request)
    {
        $query = Alumni::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('achievement', 'like', "%{$search}%")
                  ->orWhere('motto', 'like', "%{$search}%");
            });
        }

        if ($year = $request->input('year')) {
            $query->where('graduation_year', $year);
        }

        $alumni = $query->ordered()->paginate(15)->withQueryString();

        $years = Alumni::query()
            ->whereNotNull('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        return view('admin.alumni.index', compact('alumni', 'years'));
    }

    public function create()
    {
        return view('admin.alumni.create', [
            'departments' => $this->getDepartments(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'alumni');
        }

        $alumni = Alumni::create($validated);
        $this->logActivity('create', 'alumni', "Menambahkan alumni: {$alumni->name}");

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumnus)
    {
        return view('admin.alumni.edit', [
            'alumni' => $alumnus,
            'departments' => $this->getDepartments(),
        ]);
    }

    public function update(Request $request, Alumni $alumnus)
    {
        $validated = $this->validateData($request);

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'alumni', $alumnus->photo);
        }

        $alumnus->update($validated);
        $this->logActivity('update', 'alumni', "Memperbarui alumni: {$alumnus->name}");

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function destroy(Alumni $alumnus)
    {
        $name = $alumnus->name;

        if ($alumnus->photo && Storage::disk('public')->exists($alumnus->photo)) {
            Storage::disk('public')->delete($alumnus->photo);
        }

        $alumnus->delete();
        $this->logActivity('delete', 'alumni', "Menghapus alumni: {$name}");

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }

    /**
     * Bulk action (hapus massal) — pakai trait BulkActionable.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Alumni::class,
            'alumni',
            ['delete_files' => ['photo']]
        );
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name'             => 'required|string|max:255',
            'photo'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'graduation_year'  => 'nullable|integer|min:1990|max:' . (date('Y') + 1),
            'department'       => 'nullable|string|max:100',
            'achievement'      => 'nullable|string|max:255',
            'motto'            => 'nullable|string|max:255',
            'description'      => 'nullable|string|max:1000',
        ]);
    }

    protected function getDepartments()
    {
        return Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);
    }
}