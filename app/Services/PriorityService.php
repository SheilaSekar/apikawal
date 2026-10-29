<?php

namespace App\Services;

use App\Domain\PriorityCalculator;
use App\Enums\VolunteerStatus;
use App\Models\Hotspot;
use App\Models\PointOfInterest;
use App\Models\PriorityConfig;
use App\Models\Report;
use App\Support\Geo;
use Illuminate\Support\Facades\Cache;

class PriorityService
{
    public function recalculate(Report $report): array
    {
        $report->loadMissing(['verifications.user', 'photos', 'children']);
        $confirm = 0;
        $deny = 0;
        foreach ($report->verifications as $vote) {
            $role = $vote->user->role->value;
            if ($role === 'relawan' && $vote->user->volunteer_status !== VolunteerStatus::Approved) {
                $role = 'warga';
            }
            if ($vote->vote->value === 'konfirmasi') {
                $confirm += $vote->weight;
            } elseif ($vote->vote->value === 'tidak_terlihat') {
                $deny += $vote->weight;
            }
        }
        $ageHours = max(0, now()->diffInSeconds($report->created_at, true) / 3600);
        $result = PriorityCalculator::calculate([
            'confirm_w' => $confirm, 'deny_w' => $deny, 'land_type' => $report->land_type->value,
            'hotspot_distance_km' => $this->nearestHotspot($report), 'poi_distance_km' => $this->nearestPoi($report),
            'duplicate_count' => $report->children->count(), 'age_hours' => $ageHours,
            'has_photo' => $report->photos->isNotEmpty(), 'status' => $report->status->value,
        ], $this->weights());
        $report->update(['priority_score' => $result['score'], 'priority_level' => $result['level'], 'priority_breakdown' => $result['breakdown']]);

        return $result;
    }

    public function weights(): array
    {
        return Cache::remember('apikawal.priority.weights', 3600, function (): array {
            $stored = PriorityConfig::query()->pluck('value', 'name')->all();

            return $stored ?: config('apikawal.priority.weights');
        });
    }

    public function clearWeightCache(): void
    {
        Cache::forget('apikawal.priority.weights');
    }

    private function nearestHotspot(Report $report): ?float
    {
        $since = now()->subHours(24);
        $box = Geo::boundingBox($report->lat, $report->lng, 5);
        $points = Hotspot::query()->where('detected_at', '>=', $since)->whereBetween('lat', [$box['min_lat'], $box['max_lat']])
            ->whereBetween('lng', [$box['min_lng'], $box['max_lng']])->get(['lat', 'lng']);

        return $points->isEmpty() ? null : $points->min(fn (Hotspot $point) => Geo::haversineKm($report->lat, $report->lng, $point->lat, $point->lng));
    }

    private function nearestPoi(Report $report): ?float
    {
        $box = Geo::boundingBox($report->lat, $report->lng, 5);
        $points = PointOfInterest::query()->whereBetween('lat', [$box['min_lat'], $box['max_lat']])->whereBetween('lng', [$box['min_lng'], $box['max_lng']])->get(['lat', 'lng']);

        return $points->isEmpty() ? null : $points->min(fn (PointOfInterest $point) => Geo::haversineKm($report->lat, $report->lng, $point->lat, $point->lng));
    }
}
