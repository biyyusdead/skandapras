<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    public function index(): View
    {
        $admission = Admission::active()->latest()->first();

        return view('public.akademik.spmb.index', compact('admission'));
    }

    public function panduan(): View
    {
        $admission = Admission::active()->latest()->first();

        return view('public.akademik.spmb.panduan', compact('admission'));
    }
}
