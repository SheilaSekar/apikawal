<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreAbuseFlagRequest;
use App\Models\Report;
use App\Services\AbuseReportService;
use Illuminate\Http\RedirectResponse;

class ReportAbuseController extends Controller
{
    public function store(StoreAbuseFlagRequest $request, Report $report, AbuseReportService $abuse): RedirectResponse
    {
        $abuse->flag($request->user(), $report, $request->validated('reason'));

        return back()->with('status', 'Terima kasih. Laporan penyalahgunaan telah dikirim untuk ditinjau.');
    }
}
