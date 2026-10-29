<?php

namespace Tests\Unit;

use App\Domain\DuplicateMatcher;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DuplicateMatcherTest extends TestCase
{
    #[Test]
    public function it_matches_a_nearby_open_report_inside_the_window(): void
    {
        $now = new \DateTimeImmutable('2026-10-29 12:00:00');
        $result = DuplicateMatcher::find(-3.2, 105.3, 2, [[
            'id' => 10, 'reporter_id' => 1, 'lat' => -3.201, 'lng' => 105.301,
            'status' => 'baru', 'created_at' => $now->modify('-2 hours'),
        ]], $now);

        $this->assertSame('duplicate', $result['kind']);
        $this->assertSame(10, $result['report']['id']);
    }

    #[Test]
    public function it_rejects_the_same_reporter_and_ignores_closed_or_old_reports(): void
    {
        $now = new \DateTimeImmutable('2026-10-29 12:00:00');
        $sameReporter = DuplicateMatcher::find(-3.2, 105.3, 1, [[
            'id' => 1, 'reporter_id' => 1, 'lat' => -3.2, 'lng' => 105.3,
            'status' => 'baru', 'created_at' => $now,
        ]], $now);
        $closed = DuplicateMatcher::find(-3.2, 105.3, 2, [[
            'id' => 2, 'reporter_id' => 1, 'lat' => -3.2, 'lng' => 105.3,
            'status' => 'selesai', 'created_at' => $now->modify('-1 hour'),
        ]], $now);

        $this->assertSame('same_reporter', $sameReporter['kind']);
        $this->assertSame('none', $closed['kind']);
    }
}
