<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Alumni;
use App\Models\Department;
use App\Models\News;
use App\Models\Room;
use App\Models\SchoolProfile;
use App\Models\Teacher;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // ✅ Cache HANYA stats (array of integers — aman)
        $stats = Cache::remember('home.stats', 300, function () {
            return [
                'achievements' => Achievement::count(),
                'news'         => News::published()->count(),
                'teachers'     => Teacher::where('is_active', true)->count(),
                'rooms'        => Room::where('is_active', true)->count(),
            ];
        });

        return view('public.home', [
            'school'            => SchoolProfile::current(),
            'departments'       => Department::active()->orderBy('name')->take(4)->get(),
            'news'              => News::published()->with('author:id,name')->latest('published_at')->take(6)->get(),
            'alumni'            => Alumni::ordered()->take(6)->get(),
            'totalAchievements' => $stats['achievements'],
            'totalNews'         => $stats['news'],
            'totalTeachers'     => $stats['teachers'],
            'totalRooms'        => $stats['rooms'],
        ]);
    }
}