<?php

namespace App\Http\Controllers\Waka;

use App\Http\Controllers\Controller;
use App\Models\Approval;
use App\Models\DamageReport;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    public function index(Request $request): View
    {
        // Laporan yang butuh tindakan Waka (menunggu_verifikasi atau diverifikasi)
        $pending = DamageReport::query()
            ->with(['room:id,name', 'reporter:id,name'])
            ->whereIn('status', ['menunggu_verifikasi', 'diverifikasi', 'menunggu_persetujuan_1'])
            ->latest()
            ->paginate(15);

        // Riwayat approval Waka (sudah approved/rejected oleh Waka)
        $history = Approval::query()
            ->with(['damageReport:id,report_code,room_id', 'damageReport.room:id,name'])
            ->where('approval_level', 1)
            ->where('approver_id', auth()->id())
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->paginate(15, ['*'], 'history_page');

        return view('waka.approvals.index', compact('pending', 'history'));
    }
}
