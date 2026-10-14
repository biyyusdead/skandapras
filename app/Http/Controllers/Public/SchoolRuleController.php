<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SchoolRule;
use Illuminate\View\View;

class SchoolRuleController extends Controller
{
    public function index(): View
    {
        $rules = SchoolRule::active()->latest()->get();

        return view('public.kesiswaan.tata-tertib.index', compact('rules'));
    }
}