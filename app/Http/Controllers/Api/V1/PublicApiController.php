<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\IndexPublicReportsRequest;
use App\Http\Resources\ReportPublicResource;
use App\Http\Resources\RiskAreaResource;
use App\Models\GridCell;
use App\Models\Report;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublicApiController extends Controller
{
    public function reports(IndexPublicReportsRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $query = Report::query()->where('needs_moderation', false)->whereNotIn('status', [ReportStatus::Ditolak, ReportStatus::Duplikat]);
        foreach (['status', 'priority_level' => 'level', 'land_type'] as $column => $input) {
            $key = is_int($column) ? $input : $column;
            $param = is_int($column) ? $input : $input;
            if (isset($filters[$param])) {
                $query->where($key, $filters[$param]);
            }
        }
        if (! empty($filters['since'])) {
            $query->where('created_at', '>=', $filters['since']);
        }
        $page = $query->latest()->paginate((int) ($filters['per_page'] ?? 20));

        return response()->json(['data' => ReportPublicResource::collection($page)->resolve(), 'meta' => ['current_page' => $page->currentPage(), 'last_page' => $page->lastPage(), 'per_page' => $page->perPage(), 'total' => $page->total()]])->header('Cache-Control', 'public, max-age=30');
    }

    public function report(Report $report): JsonResponse
    {
        abort_if($report->needs_moderation || in_array($report->status, [ReportStatus::Ditolak, ReportStatus::Duplikat], true), 404);
        $report->load('logs');

        return response()->json(['data' => array_merge((new ReportPublicResource($report))->resolve(), [
            'timeline' => $report->logs->map(fn ($log) => ['status' => $log->to_status, 'note' => $log->note, 'at' => $log->created_at->toIso8601String()]),
        ]), 'meta' => (object) []])->header('Cache-Control', 'public, max-age=30');
    }

    public function riskAreas(IndexPublicReportsRequest $request): JsonResponse
    {
        $query = GridCell::query()->whereNotNull('prone_level');
        if ($request->filled('level')) {
            $query->where('prone_level', $request->validated('level'));
        }
        $data = RiskAreaResource::collection($query->orderBy('rank_position')->limit(1000)->get())->resolve();

        return response()->json(['data' => $data, 'meta' => ['count' => count($data)]])->header('Cache-Control', 'public, max-age=300');
    }

    public function stats(AnalyticsService $analytics): JsonResponse
    {
        $data = Cache::remember('apikawal.public.stats', 60, fn () => $analytics->summary());

        return response()->json(['data' => $data, 'meta' => ['generated_at' => now()->toIso8601String()]])->header('Cache-Control', 'public, max-age=60');
    }

    public function health(): JsonResponse
    {
        try {
            DB::select('select 1');
            $database = 'ok';
        } catch (\Throwable) {
            $database = 'error';
        }

        return response()->json(['data' => ['status' => $database === 'ok' ? 'ok' : 'degraded', 'database' => $database, 'version' => app()->version(), 'time' => now()->toIso8601String()], 'meta' => (object) []], $database === 'ok' ? 200 : 503);
    }
}
