<?php

namespace App\Http\Controllers\Report;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Report\CheckDuplicateRequest;
use App\Http\Requests\Report\StoreReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use App\Support\Geo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(): View
    {
        $this->authorize('create', Report::class);

        return view('pages.reports.create');
    }

    public function store(StoreReportRequest $request, ReportService $reports): RedirectResponse
    {
        $this->authorize('create', Report::class);
        $report = $reports->create($request->user(), $request->validated());
        $message = $report->parent_id ? 'Laporan serupa sudah ada. Laporanmu menambah dukungan.' : 'Laporan berhasil dikirim.';

        return redirect()->route('reports.show', $report->code)->with('status', $message);
    }

    public function checkDuplicate(CheckDuplicateRequest $request, ReportService $reports): JsonResponse
    {
        return response()->json($reports->checkDuplicate($request->user(), (float) $request->validated('lat'), (float) $request->validated('lng')));
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);
        $user = auth()->user();
        $report->load(['photos', 'verifications', 'logs', 'children', 'parent']);
        $isOfficer = $user && in_array($user->role, [Role::Petugas, Role::Admin], true);
        $canVerify = $user && $user->id !== $report->reporter_id
            && in_array($report->status, [ReportStatus::Baru, ReportStatus::Terverifikasi], true)
            && ! $report->verifications->contains('user_id', $user->id);

        return view('pages.reports.detail', [
            'report' => $report,
            'latitude' => $isOfficer ? $report->lat : Geo::roundPublicCoordinate($report->lat),
            'longitude' => $isOfficer ? $report->lng : Geo::roundPublicCoordinate($report->lng),
            'isOfficer' => $isOfficer,
            'canVerify' => $canVerify,
            'voteCounts' => $report->verifications->groupBy(fn ($vote) => $vote->vote->value)->map->count(),
        ]);
    }
}
