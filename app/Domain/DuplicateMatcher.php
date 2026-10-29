<?php

namespace App\Domain;

use App\Support\Geo;

final class DuplicateMatcher
{
    /** Menerapkan aturan radius/waktu tanpa query database agar mudah diuji. */
    public static function find(float $lat, float $lng, int $reporterId, iterable $candidates, ?\DateTimeInterface $now = null): array
    {
        $now ??= new \DateTimeImmutable;
        $cutoff = $now->getTimestamp() - (int) config('apikawal.duplicate.window_hours', 6) * 3600;
        $radius = (float) config('apikawal.duplicate.radius_km', 0.3);
        $closed = config('apikawal.duplicate.closed_statuses', []);
        $matches = [];

        foreach ($candidates as $candidate) {
            $createdAt = $candidate['created_at'] instanceof \DateTimeInterface ? $candidate['created_at']->getTimestamp() : strtotime((string) $candidate['created_at']);
            if ($createdAt < $cutoff || $createdAt > $now->getTimestamp() || in_array($candidate['status'], $closed, true)) {
                continue;
            }
            $distance = Geo::haversineKm($lat, $lng, (float) $candidate['lat'], (float) $candidate['lng']);
            if ($distance <= $radius) {
                $matches[] = ['candidate' => $candidate, 'distance' => $distance];
            }
        }
        foreach ($matches as $match) {
            if ((int) $match['candidate']['reporter_id'] === $reporterId) {
                return ['kind' => 'same_reporter', 'report' => $match['candidate'], 'distance_km' => $match['distance']];
            }
        }
        usort($matches, static fn (array $a, array $b): int => $a['distance'] <=> $b['distance']);

        return isset($matches[0]) ? ['kind' => 'duplicate', 'report' => $matches[0]['candidate'], 'distance_km' => $matches[0]['distance']] : ['kind' => 'none'];
    }
}
