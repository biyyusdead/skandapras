<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Traits\BulkActionable;

class ExtracurricularController extends Controller
{
    use LogsActivity, BulkActionable;
    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Extracurricular::class,
            'extracurriculars',
            ['delete_files' => ['image']]
        );
    }

    public function index(Request $request): View
    {
        $extracurriculars = Extracurricular::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.extracurriculars.index', compact('extracurriculars'));
    }

    public function create(): View
    {
        return view('admin.extracurriculars.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'extracurriculars');
        }

        $e = Extracurricular::create($validated);
        $this->logActivity('create', 'extracurriculars', "Menambahkan ekstrakurikuler: {$e->name}");

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil ditambahkan.');
    }

    public function edit(Extracurricular $extracurricular): View
    {
        return view('admin.extracurriculars.edit', compact('extracurricular'));
    }

    public function update(Request $request, Extracurricular $extracurricular): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'extracurriculars', $extracurricular->image);
        }

        $extracurricular->update($validated);
        $this->logActivity('update', 'extracurriculars', "Memperbarui ekstrakurikuler: {$extracurricular->name}");

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil diperbarui.');
    }

    public function destroy(Extracurricular $extracurricular): RedirectResponse
    {
        $name = $extracurricular->name;
        if ($extracurricular->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($extracurricular->image);
        }
        $extracurricular->delete();
        $this->logActivity('delete', 'extracurriculars', "Menghapus ekstrakurikuler: {$name}");

        return redirect()->route('admin.extracurriculars.index')->with('success', 'Ekstrakurikuler berhasil dihapus.');
    }
}