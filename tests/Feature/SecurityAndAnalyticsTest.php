<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Enums\Role;
use App\Models\GridCell;
use App\Models\Report;
use App\Models\User;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present_on_public_pages(): void
    {
        $response = $this->get('/')->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff')->assertHeader('X-Frame-Options', 'DENY')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString('frame-ancestors', $response->baseResponse->headers->get('Content-Security-Policy'));
    }

    public function test_low_reputation_report_is_hidden_for_moderation(): void
    {
        $user = User::factory()->create(['reputation' => -3]);
        $this->actingAs($user)->post('/lapor', ['lat' => -3.2, 'lng' => 105.3, 'land_type' => 'gambut', 'signs' => ['asap']])->assertRedirect();
        $report = Report::firstOrFail();
        $this->assertTrue($report->needs_moderation);
        $this->assertDatabaseHas('abuse_flags', ['report_id' => $report->id, 'resolved' => false]);
        $this->actingAs(User::factory()->create())->get('/laporan/'.$report->code)->assertForbidden();
    }

    public function test_warga_can_submit_volunteer_application_and_admin_can_approve(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('profile.volunteer.apply'), ['motivation' => 'Saya siap membantu verifikasi laporan di wilayah saya.'])->assertRedirect();
        $this->assertSame('pending', $user->fresh()->volunteer_status->value);

        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();
        $this->actingAs($admin)->get(route('admin.index'))->assertOk()->assertSee('Pengajuan relawan');
        $this->patch(route('admin.users.update', $user), ['role' => 'relawan', 'is_active' => 1])->assertRedirect();
        $this->assertSame('approved', $user->fresh()->volunteer_status->value);
        $this->patch(route('admin.users.update', $user), ['role' => 'relawan', 'is_active' => 1, 'volunteer_status' => 'rejected'])->assertRedirect();
        $this->assertSame('rejected', $user->fresh()->volunteer_status->value);
        $this->assertSame('warga', $user->fresh()->role->value);
    }

    public function test_grid_has_exact_bbox_cell_count_and_counts_only_verified_reports(): void
    {
        Report::factory()->create(['lat' => -3.599, 'lng' => 105.001, 'status' => ReportStatus::Terverifikasi]);
        Report::factory()->create(['lat' => -3.598, 'lng' => 105.002, 'status' => ReportStatus::Baru]);
        $this->assertSame(192, app(AnalyticsService::class)->recomputeGrid());
        $first = GridCell::where('cell_key', '0_0')->firstOrFail();
        $this->assertSame(1, $first->report_count);
    }
}
