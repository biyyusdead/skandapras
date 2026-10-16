<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction($request, Internship::class, 'internships');
    }

    public function index(Request $request): View
    {
        $internships = Internship::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('company', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.internships.index', compact('internships'));
    }

    public function create(): View
    {
        return view('admin.internships.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $i = Internship::create($validated);
        $this->logActivity('create', 'internships', "Menambahkan PKL/BKK: {$i->title}");

        return redirect()->route('admin.internships.index')->with('success', 'Data PKL & BKK berhasil ditambahkan.');
    }

    public function edit(Internship $internship): View
    {
        return view('admin.internships.edit', compact('internship'));
    }

    public function update(Request $request, Internship $internship): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $internship->update($validated);
        $this->logActivity('update', 'internships', "Memperbarui PKL/BKK: {$internship->title}");

        return redirect()->route('admin.internships.index')->with('success', 'Data PKL & BKK berhasil diperbarui.');
    }

    public function destroy(Internship $internship): RedirectResponse
    {
        $title = $internship->title;
        $internship->delete();
        $this->logActivity('delete', 'internships', "Menghapus PKL/BKK: {$title}");

        return redirect()->route('admin.internships.index')->with('success', 'Data PKL & BKK berhasil dihapus.');
    }
}
