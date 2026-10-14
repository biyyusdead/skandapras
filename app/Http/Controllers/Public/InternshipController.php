<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternshipController extends Controller
{
    public function index(Request $request): View
    {
        $internships = Internship::query()
            ->active()
            ->when($request->search, fn ($q, $s) => $q->where('title', 'like', "%{$s}%")
                ->orWhere('company', 'like', "%{$s}%"))
            ->when($request->type, fn ($q, $t) => $q->where('title', 'like', "%{$t}%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('public.kesiswaan.pkl-bkk.index', compact('internships'));
    }

    public function show(Internship $internship): View
    {
        abort_unless($internship->is_active, 404);

        $others = Internship::active()
            ->where('id', '!=', $internship->id)
            ->latest()
            ->take(4)
            ->get();

        return view('public.kesiswaan.pkl-bkk.show', compact('internship', 'others'));
    }
}