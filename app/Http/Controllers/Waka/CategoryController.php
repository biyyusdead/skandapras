<?php

namespace App\Http\Controllers\Waka;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use BulkActionable, LogsActivity;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            Category::class,
            'categories',
            ['protect_relations' => ['items']]
        );
    }

    public function index(Request $request): View
    {
        $categories = Category::query()
            ->withCount('items')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('waka.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('waka.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $c = Category::create($validated);
        $this->logActivity('create', 'categories', "Menambahkan kategori: {$c->name}");

        return redirect()->route('waka.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        return view('waka.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);
        $this->logActivity('update', 'categories', "Memperbarui kategori: {$category->name}");

        return redirect()->route('waka.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Cegah hapus kalau masih dipakai
        if ($category->items()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        $name = $category->name;
        $category->delete();
        $this->logActivity('delete', 'categories', "Menghapus kategori: {$name}");

        return redirect()->route('waka.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
