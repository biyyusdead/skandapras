<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $teachers = Teacher::query()
            ->where('is_active', true)
            ->when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('nip', 'like', "%{$s}%");
            }))
            ->when($request->competency, fn ($q, $c) => $q->where('competency', $c))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $competencies = Teacher::where('is_active', true)
            ->whereNotNull('competency')
            ->distinct()
            ->orderBy('competency')
            ->pluck('competency');

        return view('public.akademik.guru.index', compact('teachers', 'competencies'));
    }
}
