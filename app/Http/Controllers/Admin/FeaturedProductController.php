<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProduct;
use App\Traits\BulkActionable;
use App\Traits\LogsActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeaturedProductController extends Controller
{
    use LogsActivity, BulkActionable;

    public function bulkAction(Request $request): RedirectResponse
    {
        return $this->handleBulkAction(
            $request,
            FeaturedProduct::class,
            'products',
            ['delete_files' => ['image']]
        );
    }

    public function index(Request $request): View
    {
        $products = FeaturedProduct::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->status !== null && $request->status !== '', fn ($q) => $q->where('is_active', $request->status === '1'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:20', 'unique:featured_products,code'],
            'contact'     => ['nullable', 'string', 'max:30'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'products');
        }

        $p = FeaturedProduct::create($validated);
        $this->logActivity('create', 'products', "Menambahkan produk: {$p->name}");

        return redirect()->route('admin.products.index')->with('success', 'Produk unggulan berhasil ditambahkan.');
    }

    public function edit(FeaturedProduct $product): View
    {
        return view('admin.products.edit', compact('product'));
    }


    public function update(Request $request, FeaturedProduct $product): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:20',
                Rule::unique('featured_products', 'code')->ignore($product->id)],
            'contact'     => ['nullable', 'string', 'max:30'],
            'image'       => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'is_active'   => ['boolean'],
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = handle_upload($request->file('image'), 'products', $product->image);
        }

        $product->update($validated);
        $this->logActivity('update', 'products', "Memperbarui produk: {$product->name}");

        return redirect()->route('admin.products.index')->with('success', 'Produk unggulan berhasil diperbarui.');
    }

    public function destroy(FeaturedProduct $product): RedirectResponse
    {
        $name = $product->name;
        if ($product->image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        $this->logActivity('delete', 'products', "Menghapus produk: {$name}");

        return redirect()->route('admin.products.index')->with('success', 'Produk unggulan berhasil dihapus.');
    }
}