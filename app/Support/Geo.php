<?php

namespace App\Support;

final class Geo
{
    private const EARTH_RADIUS_KM = 6371.0088;

    /** Menghitung jarak dua koordinat tanpa ekstensi spasial agar hasil sama di SQLite/MySQL. */
    public static function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $toRadians = static fn (float $degrees): float => deg2rad($degrees);
        $dLat = $toRadians($lat2 - $lat1);
        $dLng = $toRadians($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 + cos($toRadians($lat1)) * cos($toRadians($lat2)) * sin($dLng / 2) ** 2;

        return 2 * self::EARTH_RADIUS_KM * asin(sqrt(min(1, $a)));
    }

    /** Bounding box awal untuk menyaring kandidat sebelum menghitung Haversine. */
    public static function boundingBox(float $lat, float $lng, float $radiusKm): array
    {
        $latDelta = $radiusKm / 110.574;
        $cosine = max(0.000001, cos(deg2rad($lat)));
        $lngDelta = $radiusKm / (111.320 * $cosine);

        return ['min_lat' => $lat - $latDelta, 'max_lat' => $lat + $latDelta,
            'min_lng' => $lng - $lngDelta, 'max_lng' => $lng + $lngDelta];
    }

    public static function roundPublicCoordinate(float $coordinate): float
    {
        return round($coordinate, 3);
    }

    public static function gridKey(float $lat, float $lng, float $cellSize = 0.05): string
    {
        return floor($lat / $cellSize).'_'.floor($lng / $cellSize);
    }
}
