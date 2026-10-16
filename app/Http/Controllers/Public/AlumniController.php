<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Alumni;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlumniController extends Controller
{
    public function index(Request $request): View
    {
        $alumni = Alumni::query()
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                        ->orWhere('department', 'like', "%{$s}%")
                        ->orWhere('achievement', 'like', "%{$s}%")
                        ->orWhere('motto', 'like', "%{$s}%");
                });
            })
            ->when($request->year, fn ($q, $y) => $q->where('graduation_year', $y))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $years = Alumni::query()
            ->whereNotNull('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        return view('public.alumni.index', compact('alumni', 'years'));
    }

    public function show(Alumni $alumni): View
    {
        $related = Alumni::query()
            ->where('id', '!=', $alumni->id)
            ->when($alumni->department, fn ($q) => $q->where('department', $alumni->department))
            ->ordered()
            ->take(3)
            ->get();

        if ($related->isEmpty()) {
            $related = Alumni::query()
                ->where('id', '!=', $alumni->id)
                ->ordered()
                ->take(3)
                ->get();
        }

        return view('public.alumni.show', compact('alumni', 'related'));
    }
}
