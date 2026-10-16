<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction($request, Admission::class, 'admissions');
    }

    public function index(Request $request): View
    {
        $admissions = Admission::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.admissions.index', compact('admissions'));
    }

    public function create(): View
    {
        return view('admin.admissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'schedule' => ['nullable', 'string'],
            'additional_information' => ['nullable', 'string'],
            'official_link' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $a = Admission::create($validated);
        $this->logActivity('create', 'admissions', "Menambahkan SPMB: {$a->title}");

        return redirect()->route('admin.admissions.index')->with('success', 'SPMB berhasil ditambahkan.');
    }

    public function edit(Admission $admission): View
    {
        return view('admin.admissions.edit', compact('admission'));
    }

    public function update(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'schedule' => ['nullable', 'string'],
            'additional_information' => ['nullable', 'string'],
            'official_link' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $admission->update($validated);
        $this->logActivity('update', 'admissions', "Memperbarui SPMB: {$admission->title}");

        return redirect()->route('admin.admissions.index')->with('success', 'SPMB berhasil diperbarui.');
    }

    public function destroy(Admission $admission): RedirectResponse
    {
        $title = $admission->title;
        $admission->delete();
        $this->logActivity('delete', 'admissions', "Menghapus SPMB: {$title}");

        return redirect()->route('admin.admissions.index')->with('success', 'SPMB berhasil dihapus.');
    }
}
