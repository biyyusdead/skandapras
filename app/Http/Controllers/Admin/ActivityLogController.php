<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user:id,name,email')
            ->when($request->search, fn ($q, $s) => $q->where('description', 'like', "%{$s}%"))
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->when($request->module, fn ($q, $m) => $q->where('module', $m))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $actions = ActivityLog::distinct()->orderBy('action')->pluck('action');
        $modules = ActivityLog::whereNotNull('module')->distinct()->orderBy('module')->pluck('module');

        return view('admin.logs.index', compact('logs', 'actions', 'modules'));
    }
}
