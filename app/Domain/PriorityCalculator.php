<?php

namespace App\Domain;

final class PriorityCalculator
{
    public static function calculate(array $input, array $weights, array $landFactors, int $unverifiedCap): array
    {
        $age = max(0, (float) ($input['age_hours'] ?? 0));
        $recency = $age < 6 ? 1 : self::clamp((48 - $age) / 42);
        $factors = [
            'verification' => self::clamp(((int) ($input['confirm_w'] ?? 0) - (int) ($input['deny_w'] ?? 0)) / 3),
            'land_type' => (float) ($landFactors[$input['land_type'] ?? 'lainnya'] ?? 0.3),
            'hotspot' => self::proximity($input['hotspot_distance_km'] ?? null),
            'poi' => self::proximity($input['poi_distance_km'] ?? null),
            'support' => 0.6 * min(1, max(0, (int) ($input['duplicate_count'] ?? 0)) / 4) + 0.4 * $recency,
            'photo' => ! empty($input['has_photo']) ? 1.0 : 0.0,
        ];
        $weightKeys = ['verification' => 'w_verification', 'land_type' => 'w_landtype', 'hotspot' => 'w_hotspot', 'poi' => 'w_poi', 'support' => 'w_support', 'photo' => 'w_photo'];
        $breakdown = [];
        $score = 0.0;
        foreach ($factors as $key => $factor) {
            $weight = (float) ($weights[$weightKeys[$key]] ?? 0);
            $contribution = $factor * $weight;
            $breakdown[$key] = ['factor' => $factor, 'weight' => $weight, 'contribution' => $contribution];
            $score += $contribution;
        }

        $score = (int) round($score);
        $unverified = ($input['status'] ?? 'baru') === 'baru';
        if ($unverified) {
            $score = min($score, $unverifiedCap);
        }
        $level = match (true) {
            $score >= 80 => 'kritis', $score >= 60 => 'tinggi', $score >= 40 => 'sedang', default => 'rendah',
        };

        return ['score' => $score, 'level' => $level, 'breakdown' => $breakdown, 'unverified' => $unverified];
    }

    private static function proximity(?float $distanceKm): float
    {
        if ($distanceKm === null || $distanceKm >= 5) {
            return 0;
        }

        return $distanceKm <= 1 ? 1 : (5 - $distanceKm) / 4;
    }

    private static function clamp(float $value): float
    {
        return min(1, max(0, $value));
    }
}
