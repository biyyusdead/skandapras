<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function index(Request $request): View
    {
        $achievements = Achievement::query()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->when($request->year, fn ($q, $y) => $q->whereYear('achievement_date', $y))
            ->latest('achievement_date')
            ->paginate(12)
            ->withQueryString();

        $categories = Achievement::distinct()->whereNotNull('category')->orderBy('category')->pluck('category');
        $years = Achievement::selectRaw('YEAR(achievement_date) as year')
            ->whereNotNull('achievement_date')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $stats = [
            'total'      => Achievement::count(),
            'nasional'   => Achievement::where('category', 'Nasional')->count(),
            'provinsi'   => Achievement::where('category', 'Provinsi')->count(),
            'kabupaten'  => Achievement::where('category', 'Kabupaten')->count(),
        ];

        return view('public.kesiswaan.prestasi.index', compact('achievements', 'categories', 'years', 'stats'));
    }
        public function show(Achievement $achievement): View
    {
        $related = Achievement::query()
            ->where('id', '!=', $achievement->id)
            ->when($achievement->category, fn ($q) => $q->where('category', $achievement->category))
            ->latest('achievement_date')
            ->take(3)
            ->get();

        // Kalau related kurang dari 3, ambil dari kategori lain
        if ($related->count() < 3) {
            $more = Achievement::where('id', '!=', $achievement->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->latest('achievement_date')
                ->take(3 - $related->count())
                ->get();

            $related = $related->concat($more);
        }

        return view('public.kesiswaan.prestasi.show', compact('achievement', 'related'));
    }
}