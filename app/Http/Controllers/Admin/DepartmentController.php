<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction($request, Department::class, 'departments');
    }

    public function index(Request $request): View
    {
        $departments = Department::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('code', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:departments,code'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'departments');
        }

        $dept = Department::create($validated);
        $this->logActivity('create', 'departments', "Menambahkan jurusan: {$dept->name}");

        return redirect()->route('admin.departments.index')
            ->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($department->id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'departments', $department->image);
        }

        $department->update($validated);
        $this->logActivity('update', 'departments', "Memperbarui jurusan: {$department->name}");

        return redirect()->route('admin.departments.index')
            ->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $name = $department->name;
        $department->delete();
        $this->logActivity('delete', 'departments', "Menghapus jurusan: {$name}");

        return redirect()->route('admin.departments.index')
            ->with('success', 'Jurusan berhasil dihapus.');
    }
}
