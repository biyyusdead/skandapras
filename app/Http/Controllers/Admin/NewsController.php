<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewsController extends Controller
{
    use LogsActivity, BulkActionable;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            News::class,
            'news',
            ['delete_files' => ['image']]
        );
    }
    public function index(Request $request): View
    {
        $news = News::with('author:id,name')
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.news.index', compact('news'));
    }

    public function create(): View
    {
        return view('admin.news.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'image'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'status'       => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        

        $validated['author_id'] = auth()->id();
        $validated['slug']      = Str::slug($validated['title']) . '-' . Str::random(5);

        $validated['published_at'] = $validated['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'news');
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $n = News::create($validated);
        $this->logActivity('create', 'news', "Menambahkan berita: {$n->title}");

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(News $news): View
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'image'        => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:3072'],
            'status'       => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        $validated['published_at'] = $validated['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'news', $news->image);
        }

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = $news->published_at ?? now();
        }

        $news->update($validated);
        $this->logActivity('update', 'news', "Memperbarui berita: {$news->title}");

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $title = $news->title;
        if ($news->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($news->image);
        }
        $news->delete();
        $this->logActivity('delete', 'news', "Menghapus berita: {$title}");

        return redirect()->route('admin.news.index')->with('success', 'Berita berhasil dihapus.');
    }
}