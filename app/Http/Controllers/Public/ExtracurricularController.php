<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Extracurricular;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExtracurricularController extends Controller
{
    public function index(Request $request): View
    {
        $extracurriculars = Extracurricular::active()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('description', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('public.kesiswaan.ekstrakurikuler.index', compact('extracurriculars'));
    }

    public function show(Extracurricular $extracurricular): View
    {
        abort_unless($extracurricular->is_active, 404);

        $others = Extracurricular::active()
            ->where('id', '!=', $extracurricular->id)
            ->orderBy('name')
            ->take(4)
            ->get();

        return view('public.kesiswaan.ekstrakurikuler.show', compact('extracurricular', 'others'));
    }
}
