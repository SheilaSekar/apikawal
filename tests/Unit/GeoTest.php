<?php

namespace Tests\Unit;

use App\Support\Geo;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class GeoTest extends TestCase
{
    #[Test]
    public function it_measures_haversine_distance(): void
    {
        $distance = Geo::haversineKm(-6.2088, 106.8456, -6.9175, 107.6191);

        $this->assertGreaterThan(115, $distance);
        $this->assertLessThan(125, $distance);
    }

    #[Test]
    public function it_builds_a_bounding_box_around_a_point(): void
    {
        $box = Geo::boundingBox(-3.2, 105.3, 0.3);

        $this->assertLessThan(-3.2, $box['min_lat']);
        $this->assertGreaterThan(105.3, $box['max_lng']);
    }

    #[Test]
    public function it_rounds_public_coordinates_to_three_decimals(): void
    {
        $this->assertSame(-3.201, Geo::roundPublicCoordinate(-3.2007));
    }
}
