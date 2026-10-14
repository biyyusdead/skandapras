<?php

namespace App\Http\Controllers\Kepala;

use App\Enums\DamageReportStatus;
use App\Http\Controllers\Controller;
use App\Models\DamageReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DamageReportController extends Controller
{
    public function index(Request $request): View
    {
        $reports = DamageReport::query()
            ->with(['room:id,name', 'reporter:id,name'])
            ->when($request->search, fn ($q, $s) => $q->where('report_code', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kepala.damage-reports.index', [
            'reports'  => $reports,
            'statuses' => DamageReportStatus::cases(),
        ]);
    }

    public function show(DamageReport $report): View
    {
        $report->load([
            'room:id,name,code,photo',
            'itemRoom.item:id,name,code',
            'reporter:id,name,email',
            'verifier:id,name',
            'approvals.approver:id,name',
            'repairReports.creator:id,name',
        ]);

        return view('kepala.damage-reports.show', compact('report'));
    }
}