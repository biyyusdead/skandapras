<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::active()->orderBy('name')->get();

        return view('public.akademik.kompetensi.index', compact('departments'));
    }

    public function show(Department $department): View
    {
        abort_unless($department->is_active, 404);

        $others = Department::active()
            ->where('id', '!=', $department->id)
            ->orderBy('name')
            ->take(4)
            ->get();

        return view('public.akademik.kompetensi.show', compact('department', 'others'));
    }
}