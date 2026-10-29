<?php

namespace Tests\Unit;

use App\Domain\PriorityCalculator;
use App\Domain\StatusMachine;
use App\Domain\VerificationEvaluator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessRulesTest extends TestCase
{
    #[Test]
    public function it_caps_unverified_priority_at_fifty_nine_and_returns_factor_breakdown(): void
    {
        $result = PriorityCalculator::calculate([
            'confirm_w' => 3, 'deny_w' => 0, 'land_type' => 'gambut', 'age_hours' => 0,
            'duplicate_count' => 0, 'has_photo' => true, 'status' => 'baru',
        ]);

        $this->assertSame(59, $result['score']);
        $this->assertSame('sedang', $result['level']);
        $this->assertTrue($result['unverified']);
        $this->assertArrayHasKey('verification', $result['breakdown']);
    }

    #[Test]
    public function it_calculates_the_full_priority_after_verification(): void
    {
        $result = PriorityCalculator::calculate([
            'confirm_w' => 3, 'deny_w' => 0, 'land_type' => 'gambut', 'age_hours' => 0,
            'duplicate_count' => 4, 'has_photo' => true, 'hotspot_distance_km' => 0.5, 'poi_distance_km' => 1,
            'status' => 'terverifikasi',
        ]);

        $this->assertSame(100, $result['score']);
        $this->assertSame('kritis', $result['level']);
    }

    #[Test]
    public function it_evaluates_weighted_votes_and_trusted_verifiers(): void
    {
        $twoResidents = VerificationEvaluator::evaluate([
            ['vote' => 'konfirmasi', 'weight' => 1, 'role' => 'warga'],
            ['vote' => 'konfirmasi', 'weight' => 1, 'role' => 'warga'],
        ]);
        $trusted = VerificationEvaluator::evaluate([['vote' => 'konfirmasi', 'weight' => 2, 'role' => 'relawan']]);
        $denied = VerificationEvaluator::evaluate([['vote' => 'tidak_terlihat', 'weight' => 3, 'role' => 'warga']]);
        $out = VerificationEvaluator::evaluate([['vote' => 'sudah_padam', 'weight' => 2, 'role' => 'warga']]);

        $this->assertSame('terverifikasi', $twoResidents['status']);
        $this->assertSame('terverifikasi', $trusted['status']);
        $this->assertSame('ditolak', $denied['status']);
        $this->assertSame('padam_sendiri', $out['status']);
    }

    #[Test]
    public function it_enforces_response_transitions_and_admin_override_reason(): void
    {
        $this->assertTrue(StatusMachine::canTransition('terverifikasi', 'diteruskan', 'petugas', 'Ditugaskan ke regu A'));
        $this->assertTrue(StatusMachine::canTransition('terkendali', 'selesai', 'petugas'));
        $this->assertFalse(StatusMachine::canTransition('baru', 'selesai', 'petugas'));
        $this->assertFalse(StatusMachine::canTransition('baru', 'selesai', 'admin'));
        $this->assertTrue(StatusMachine::canTransition('baru', 'selesai', 'admin', 'Koreksi status hasil audit'));
    }
}
