<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('public.profile.index', [
            'school' => SchoolProfile::current(),
        ]);
    }

    public function sambutan(): View
    {
        return view('public.profile.sambutan', [
            'school' => SchoolProfile::current(),
        ]);
    }

    public function sejarah(): View
    {
        return view('public.profile.sejarah', [
            'school' => SchoolProfile::current(),
        ]);
    }

    public function struktur(): View
    {
        return view('public.profile.struktur', [
            'school' => SchoolProfile::current(),
        ]);
    }
}
