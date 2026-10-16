<?php

namespace App\Http\Controllers\Kepala;

use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Models\RepairReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairReportController extends Controller
{
    public function index(Request $request): View
    {
        $repairs = RepairReport::query()
            ->with(['damageReport:id,report_code,room_id', 'damageReport.room:id,name'])
            ->when($request->search, fn ($q, $s) => $q->where('technician_name', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $st) => $q->where('status', $st))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kepala.repair-reports.index', [
            'repairs' => $repairs,
            'statuses' => RepairStatus::cases(),
        ]);
    }
}
