<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AchievementController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Achievement::class,
            'achievements',
            ['delete_files' => ['image']]
        );
    }

    public function index(Request $request): View
    {
        $achievements = Achievement::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('category', 'like', "%{$s}%"))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->latest('achievement_date')
            ->paginate(15)
            ->withQueryString();

        $categories = Achievement::distinct()->whereNotNull('category')->orderBy('category')->pluck('category');

        return view('admin.achievements.index', compact('achievements', 'categories'));
    }

    public function create(): View
    {
        return view('admin.achievements.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'achievement_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'achievements');
        }

        $a = Achievement::create($validated);
        $this->logActivity('create', 'achievements', "Menambahkan prestasi: {$a->title}");

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil ditambahkan.');
    }

    public function edit(Achievement $achievement): View
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'achievement_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'achievements', $achievement->image);
        }

        $achievement->update($validated);
        $this->logActivity('update', 'achievements', "Memperbarui prestasi: {$achievement->title}");

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil diperbarui.');
    }

    public function destroy(Achievement $achievement): RedirectResponse
    {
        $title = $achievement->title;
        if ($achievement->image) {
            Storage::disk('public')->delete($achievement->image);
        }
        $achievement->delete();
        $this->logActivity('delete', 'achievements', "Menghapus prestasi: {$title}");

        return redirect()->route('admin.achievements.index')->with('success', 'Prestasi berhasil dihapus.');
    }
}
