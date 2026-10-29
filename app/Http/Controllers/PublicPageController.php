<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Models\Hotspot;
use App\Models\Report;
use App\Models\UserNotification;
use App\Services\PriorityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function landing(): View
    {
        $reports = Report::query()->where('needs_moderation', false)->whereNotIn('status', [ReportStatus::Ditolak, ReportStatus::Duplikat])->latest()->limit(5)->get();
        $counts = [
            'active' => Report::whereIn('status', [ReportStatus::Baru, ReportStatus::Terverifikasi, ReportStatus::Diteruskan, ReportStatus::DalamPenanganan, ReportStatus::Terkendali])->count(),
            'verified' => Report::where('status', ReportStatus::Terverifikasi)->count(),
            'handling' => Report::whereIn('status', [ReportStatus::Diteruskan, ReportStatus::DalamPenanganan])->count(),
            'done' => Report::where('status', ReportStatus::Selesai)->count(),
        ];

        return view('pages.landing', compact('reports', 'counts'));
    }

    public function map(Request $request): View
    {
        $filters = $request->only(['status', 'level', 'land_type', 'since']);
        $query = Report::query()->where('needs_moderation', false)->whereNotIn('status', [ReportStatus::Ditolak, ReportStatus::Duplikat]);
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('level')) {
            $query->where('priority_level', $request->string('level'));
        }
        if ($request->filled('land_type')) {
            $query->where('land_type', $request->string('land_type'));
        }
        if ($request->filled('since')) {
            $query->where('created_at', '>=', now()->subDays((int) $request->integer('since')));
        }
        $reports = $query->latest()->paginate(20)->withQueryString();

        return view('pages.map', compact('reports', 'filters'));
    }

    public function mapData(Request $request): JsonResponse
    {
        $user = $request->user();
        $officer = $user && in_array($user->role, [Role::Petugas, Role::Admin], true);
        $query = Report::query()->where('needs_moderation', false)->whereNotIn('status', [ReportStatus::Ditolak, ReportStatus::Duplikat]);
        foreach (['status', 'priority_level', 'land_type'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->query($filter));
            }
        }
        if ($request->filled('since')) {
            $query->where('created_at', '>=', now()->subDays((int) $request->query('since')));
        }
        $reports = $query->latest()->limit(500)->get()->map(fn ($report) => [
            'code' => $report->code, 'lat' => $officer ? $report->lat : round($report->lat, 3), 'lng' => $officer ? $report->lng : round($report->lng, 3),
            'status' => $report->status->value, 'status_label' => $report->status->label(), 'level' => $report->priority_level->value,
            'priority' => $report->priority_score, 'land_type' => $report->land_type->label(), 'simulated' => $report->is_simulated, 'created_at' => $report->created_at->format('d/m/Y H:i'),
        ]);
        $hotspots = $request->boolean('hotspots') ? Hotspot::query()->where('detected_at', '>=', now()->subDays(1))->limit(1000)->get(['lat', 'lng', 'source', 'is_simulated']) : collect();

        return response()->json(['reports' => $reports, 'hotspots' => $hotspots])->header('Cache-Control', 'private, max-age=30');
    }

    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $mine = Report::where('reporter_id', $user->id)->latest()->paginate(10, ['*'], 'mine');
        $toVerify = Report::query()->whereIn('status', [ReportStatus::Baru, ReportStatus::Terverifikasi])->where('reporter_id', '!=', $user->id)
            ->whereDoesntHave('verifications', fn ($q) => $q->where('user_id', $user->id))->latest()->limit(10)->get();
        $notifications = UserNotification::where('user_id', $user->id)->latest()->limit(10)->get();

        return view('dashboard', compact('mine', 'toVerify', 'notifications'));
    }

    public function officer(PriorityService $priorities): View
    {
        $active = Report::query()->whereIn('status', [ReportStatus::Terverifikasi, ReportStatus::Diteruskan, ReportStatus::DalamPenanganan, ReportStatus::Terkendali])
            ->orderByDesc('priority_score')->orderBy('created_at')->paginate(20);
        foreach ($active->getCollection() as $report) {
            if ($report->updated_at->lt(now()->subMinutes(30))) {
                $priorities->recalculate($report);
            }
        }

        return view('pages.officer', compact('active'));
    }

    public function readNotification(Request $request, UserNotification $notification)
    {
        abort_unless($notification->user_id === $request->user()->id, 404);
        $notification->update(['read_at' => now()]);

        return back()->with('status', 'Notifikasi ditandai telah dibaca.');
    }
}
