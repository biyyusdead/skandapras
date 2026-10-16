<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $news = News::query()
            ->published()
            ->with('author:id,name')
            ->when($request->search, function ($q, $s) use ($request) {
                $searchBy = $request->get('search_by', 'judul');

                $q->where(function ($q) use ($s, $searchBy) {
                    match ($searchBy) {
                        'penulis' => $q->whereHas('author', fn ($a) => $a->where('name', 'like', "%{$s}%")),
                        'konten' => $q->where('content', 'like', "%{$s}%"),
                        default => $q->where('title', 'like', "%{$s}%"),
                    };
                });
            })
            ->latest('published_at')
            ->paginate(6)
            ->withQueryString();

        return view('public.news.index', [
            'news' => $news,
            'searchBy' => $request->get('search_by', 'judul'),
        ]);
    }

    public function show(News $news): View
    {
        abort_unless($news->status === 'published', 404);

        $news->load('author:id,name');

        $related = News::query()
            ->published()
            ->where('id', '!=', $news->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('public.news.show', compact('news', 'related'));
    }
}
