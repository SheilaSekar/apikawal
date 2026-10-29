<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreVerificationRequest;
use App\Models\Report;
use App\Services\VerificationService;
use Illuminate\Http\RedirectResponse;

class ReportVerificationController extends Controller
{
    public function store(StoreVerificationRequest $request, Report $report, VerificationService $verification): RedirectResponse
    {
        $verification->verify($request->user(), $report, $request->validated('vote'), $request->validated('distance_km'));

        return back()->with('status', 'Verifikasi Anda berhasil dicatat.');
    }
}
