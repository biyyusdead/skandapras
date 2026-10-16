<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FeaturedProduct;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = FeaturedProduct::query()
            ->active()
            ->when($request->search, function ($q, $s) {
                $q->where(function ($qq) use ($s) {
                    $qq->where('name', 'like', "%{$s}%")
                        ->orWhere('description', 'like', "%{$s}%")
                        ->orWhere('code', 'like', "%{$s}%");
                });
            })
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->latest()
            ->get();

        $categories = FeaturedProduct::active()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('public.akademik.produk.index', compact('products', 'categories'));
    }

    public function show(FeaturedProduct $product): View
    {
        abort_unless($product->is_active, 404);

        $related = FeaturedProduct::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->when($product->category, fn ($q, $c) => $q->where('category', $c))
            ->latest()
            ->limit(3)
            ->get();

        return view('public.akademik.produk.show', compact('product', 'related'));
    }
}
