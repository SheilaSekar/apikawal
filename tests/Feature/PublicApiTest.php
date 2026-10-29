<?php

namespace Tests\Feature;

use App\Enums\ReportStatus;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_report_api_rounds_coordinates_and_never_exposes_reporter_or_description(): void
    {
        $report = Report::factory()->create(['lat' => -3.123456, 'lng' => 105.765432, 'description' => 'Identitas sensitif jangan tampil']);
        $response = $this->getJson('/api/v1/reports')->assertOk()->assertJsonPath('data.0.code', $report->code)->assertJsonPath('data.0.lat', -3.123)->assertJsonPath('data.0.lng', 105.765);
        $response->assertJsonMissingPath('data.0.reporter')->assertJsonMissingPath('data.0.description');
        $this->getJson('/api/v1/reports/'.$report->code)->assertOk()->assertJsonMissingPath('data.reporter')->assertJsonMissingPath('data.description');
    }

    public function test_public_api_hides_moderation_and_rejected_reports(): void
    {
        Report::factory()->create(['needs_moderation' => true]);
        Report::factory()->create(['status' => ReportStatus::Ditolak]);
        $this->getJson('/api/v1/reports')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_health_endpoint_reports_database_availability(): void
    {
        $this->getJson('/api/health')->assertOk()->assertJsonPath('data.database', 'ok');
    }
}
