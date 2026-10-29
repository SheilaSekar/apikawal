<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Models\GridCell;
use App\Models\Hotspot;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function summary(): array
    {
        $reports = Report::query()->where('needs_moderation', false)->with('logs')->get();
        $total = $reports->count();
        $verified = $reports->filter(fn ($report) => $report->logs->contains('to_status', 'terverifikasi'));
        $handled = $reports->filter(fn ($report) => $report->logs->contains(fn ($log) => in_array($log->to_status, ['diteruskan', 'dalam_penanganan'], true)));
        $minutes = fn ($rows, $target) => $rows->map(function ($report) use ($target) {
            $entry = $report->logs->firstWhere('to_status', $target);

            return $entry ? $report->created_at->diffInMinutes($entry->created_at) : null;
        })->filter(fn ($value) => $value !== null)->sort()->values();

        return [
            'reports' => $total,
            'verified' => $reports->where('status', ReportStatus::Terverifikasi)->count(),
            'rejected' => $reports->where('status', ReportStatus::Ditolak)->count(),
            'handling' => $reports->whereIn('status', [ReportStatus::Diteruskan, ReportStatus::DalamPenanganan])->count(),
            'done' => $reports->where('status', ReportStatus::Selesai)->count(),
            'median_report_to_verification' => $this->median($minutes($verified, 'terverifikasi')),
            'median_verification_to_handling' => $this->median($verified->map(function ($report) {
                $verification = $report->logs->firstWhere('to_status', 'terverifikasi');
                $handling = $report->logs->first(fn ($log) => in_array($log->to_status, ['diteruskan', 'dalam_penanganan'], true));

                return $verification && $handling ? $verification->created_at->diffInMinutes($handling->created_at) : null;
            })->filter(fn ($value) => $value !== null)->sort()->values()),
        ];
    }

    public function recomputeGrid(): int
    {
        $bounds = config('apikawal.demo_bounds');
        $step = (float) $bounds['grid_degrees'];
        $cells = [];
        for ($lat = (float) $bounds['south']; $lat < $bounds['north']; $lat += $step) {
            for ($lng = (float) $bounds['west']; $lng < $bounds['east']; $lng += $step) {
                $key = round($lat / $step).'_'.round($lng / $step);
                $cells[$key] = ['cell_key' => $key, 'min_lat' => $lat, 'min_lng' => $lng, 'max_lat' => min($lat + $step, $bounds['north']), 'max_lng' => min($lng + $step, $bounds['east']), 'hotspot_count' => 0, 'report_count' => 0];
            }
        }
        $period = now()->subMonths((int) config('apikawal.analytics.hotspot_months', 12));
        $hotspots = Hotspot::where('detected_at', '>=', $period)->whereBetween('lat', [$bounds['south'], $bounds['north']])->whereBetween('lng', [$bounds['west'], $bounds['east']])->get(['lat', 'lng']);
        $reports = Report::where('status', '!=', ReportStatus::Ditolak)->whereBetween('lat', [$bounds['south'], $bounds['north']])->whereBetween('lng', [$bounds['west'], $bounds['east']])->get(['lat', 'lng']);
        foreach ($hotspots as $point) {
            $this->increment($cells, $point->lat, $point->lng, 'hotspot_count', $step);
        }
        foreach ($reports as $point) {
            $this->increment($cells, $point->lat, $point->lng, 'report_count', $step);
        }
        $maxHot = max(array_column($cells, 'hotspot_count')) ?: 1;
        $maxReports = max(array_column($cells, 'report_count')) ?: 1;
        foreach ($cells as &$cell) {
            $cell['score'] = 100 * (0.7 * $cell['hotspot_count'] / $maxHot + 0.3 * $cell['report_count'] / $maxReports);
        }
        unset($cell);
        usort($cells, fn ($a, $b) => $b['score'] <=> $a['score']);
        $total = count($cells);
        foreach ($cells as $index => &$cell) {
            $cell['rank_position'] = $index + 1;
            $ratio = ($index + 1) / max(1, $total);
            $cell['prone_level'] = $ratio <= 0.2 ? 'tinggi' : ($ratio <= 0.5 ? 'sedang' : 'rendah');
        }
        unset($cell);
        DB::transaction(function () use ($cells) {
            GridCell::query()->delete();
            foreach (array_chunk($cells, 100) as $chunk) {
                GridCell::insert(array_map(fn ($cell) => $cell + ['created_at' => now(), 'updated_at' => now()], $chunk));
            }
        });

        return $total;
    }

    private function increment(array &$cells, float $lat, float $lng, string $column, float $step): void
    {
        $key = round(floor($lat / $step) * $step / $step).'_'.round(floor($lng / $step) * $step / $step);
        if (isset($cells[$key])) {
            $cells[$key][$column]++;
        }
    }

    private function median($values): ?int
    {
        if ($values->isEmpty()) {
            return null;
        }
        $middle = intdiv($values->count(), 2);

        return (int) ($values->count() % 2 ? $values[$middle] : round(($values[$middle - 1] + $values[$middle]) / 2));
    }
}
