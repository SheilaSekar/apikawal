<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReportCreationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(float $lat = -3.2, float $lng = 105.3): array
    {
        return ['lat' => $lat, 'lng' => $lng, 'land_type' => 'gambut', 'signs' => ['asap'], 'description' => 'Asap terlihat dari jalan.'];
    }

    public function test_authenticated_user_can_create_a_report_with_a_code(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/lapor', $this->payload())->assertRedirect();

        $report = Report::firstOrFail();
        $this->assertSame('baru', $report->status->value);
        $this->assertMatchesRegularExpression('/^AK-\d{4}-\d{6}$/', $report->code);
    }

    public function test_nearby_reports_are_linked_and_count_as_weighted_support(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $this->actingAs($first)->post('/lapor', $this->payload())->assertRedirect();
        $parent = Report::firstOrFail();

        $this->actingAs($second)->post('/lapor', $this->payload(-3.201, 105.301))->assertRedirect();
        $duplicate = Report::where('status', 'duplikat')->firstOrFail();
        $vote = Verification::where('report_id', $parent->id)->firstOrFail();

        $this->assertSame($parent->id, $duplicate->parent_id);
        $this->assertSame($duplicate->id, $vote->source_report_id);
        $this->assertSame('konfirmasi', $vote->vote->value);
        $this->assertSame(1, $vote->weight);
    }

    public function test_reporter_cannot_repeat_a_nearby_report(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/lapor', $this->payload())->assertRedirect();
        $this->actingAs($user)->post('/lapor', $this->payload(-3.201, 105.301))->assertSessionHasErrors('lat');
        $this->assertCount(1, Report::all());
    }

    public function test_report_requires_at_least_one_sign(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        $payload['signs'] = [];
        $this->actingAs($user)->post('/lapor', $payload)->assertSessionHasErrors('signs');
    }

    public function test_photo_is_reencoded_and_more_than_three_photos_are_rejected(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        $payload['photos'] = [UploadedFile::fake()->image('evidence.png', 640, 480)];
        $this->actingAs($user)->post('/lapor', $payload)->assertRedirect();
        $photo = Report::firstOrFail()->photos()->firstOrFail();
        $binary = base64_decode($photo->data_base64, true);
        $this->assertSame('image/jpeg', $photo->mime);
        $this->assertSame(IMAGETYPE_JPEG, getimagesizefromstring($binary)[2]);

        $secondUser = User::factory()->create();
        $payload = $this->payload(-3.4, 105.6);
        $payload['photos'] = array_map(fn ($index) => UploadedFile::fake()->image("evidence{$index}.png"), range(1, 4));
        $this->actingAs($secondUser)->post('/lapor', $payload)->assertSessionHasErrors('photos');
    }

    public function test_photos_larger_than_one_megabyte_are_rejected(): void
    {
        $user = User::factory()->create();
        $payload = $this->payload();
        $payload['photos'] = [UploadedFile::fake()->image('oversized.jpg')->size(1025)];

        $this->actingAs($user)->post('/lapor', $payload)->assertSessionHasErrors('photos.0');
        $this->assertDatabaseCount('reports', 0);
    }
}
