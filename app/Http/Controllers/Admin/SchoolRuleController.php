<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolRule;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    use LogsActivity, BulkActionable;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            SchoolRule::class,
            'rules',
            ['delete_files' => ['image']]
        );
}

    public function index(Request $request): View
    {
        $rules = SchoolRule::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.rules.index', compact('rules'));
    }

    public function create(): View
    {
        return view('admin.rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['nullable', 'string'],
            'image'     => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'rules');
        }

        $r = SchoolRule::create($validated);
        $this->logActivity('create', 'rules', "Menambahkan tata tertib: {$r->title}");

        return redirect()->route('admin.rules.index')->with('success', 'Tata tertib berhasil ditambahkan.');
    }

    public function edit(SchoolRule $rule): View
    {
        return view('admin.rules.edit', compact('rule'));
    }

    public function update(Request $request, SchoolRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'title'     => ['required', 'string', 'max:255'],
            'content'   => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'rules', $rule->image);
        }

        $rule->update($validated);
        $this->logActivity('update', 'rules', "Memperbarui tata tertib: {$rule->title}");

        return redirect()->route('admin.rules.index')->with('success', 'Tata tertib berhasil diperbarui.');
    }

    public function destroy(SchoolRule $rule): RedirectResponse
    {
        $title = $rule->title;
        $rule->delete();
        $this->logActivity('delete', 'rules', "Menghapus tata tertib: {$title}");

        return redirect()->route('admin.rules.index')->with('success', 'Tata tertib berhasil dihapus.');
    }
}