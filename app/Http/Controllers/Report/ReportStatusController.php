<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\UpdateStatusRequest;
use App\Models\Report;
use App\Services\ResponseService;
use Illuminate\Http\RedirectResponse;

class ReportStatusController extends Controller
{
    public function update(UpdateStatusRequest $request, Report $report, ResponseService $responses): RedirectResponse
    {
        $responses->transition($request->user(), $report, $request->validated('status'), $request->validated('note'));

        return back()->with('status', 'Status laporan berhasil diperbarui.');
    }
}
