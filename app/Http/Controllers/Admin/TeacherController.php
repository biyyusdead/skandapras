<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeacherController extends Controller
{
    use LogsActivity, BulkActionable;

    public function index(Request $request): View
    {
        $teachers = Teacher::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('nip', 'like', "%{$s}%")
                ->orWhere('position', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['nullable', 'string', 'max:30', 'unique:teachers,nip'],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'photo'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'=> ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'teachers');
        }

        $teacher = Teacher::create($validated);
        $this->logActivity('create', 'teachers', "Menambahkan guru: {$teacher->name}");

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher): View
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'nip'      => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($teacher->id)],
            'email'    => ['nullable', 'email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'position' => ['nullable', 'string', 'max:255'],
            'photo'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'=> ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('photo')) {
            $validated['photo'] = handle_upload($request->file('photo'), 'teachers', $teacher->photo);
        }

        $teacher->update($validated);
        $this->logActivity('update', 'teachers', "Memperbarui guru: {$teacher->name}");

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $name = $teacher->name;
        if ($teacher->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->photo);
        }
        $teacher->delete();
        $this->logActivity('delete', 'teachers', "Menghapus guru: {$name}");

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            \App\Models\Teacher::class,
            'teachers',
            ['delete_files' => ['photo']]
        );
    }
}