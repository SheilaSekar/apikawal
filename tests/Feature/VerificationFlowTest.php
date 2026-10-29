<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class VerificationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_warga_confirmations_verify_a_report_and_notify_reporter_and_officers(): void
    {
        $reporter = User::factory()->create();
        $officer = User::factory()->create();
        $officer->role = Role::Petugas;
        $officer->save();
        $report = Report::factory()->create(['reporter_id' => $reporter->id]);
        $payload = ['vote' => 'konfirmasi'];

        $this->actingAs(User::factory()->create())->post(route('reports.verify', $report->code), $payload)->assertRedirect();
        $this->assertSame('baru', $report->fresh()->status->value);
        $this->actingAs(User::factory()->create())->post(route('reports.verify', $report->code), $payload)->assertRedirect();

        $report->refresh();
        $this->assertSame('terverifikasi', $report->status->value);
        $this->assertSame(1, $reporter->fresh()->reputation);
        $this->assertDatabaseHas('response_logs', ['report_id' => $report->id, 'from_status' => 'baru', 'to_status' => 'terverifikasi']);
        $this->assertSame(1, $reporter->notifications()->count());
        $this->assertSame(1, $officer->notifications()->count());
    }

    public function test_reporter_cannot_verify_own_report_and_user_can_vote_only_once(): void
    {
        $reporter = User::factory()->create();
        $report = Report::factory()->create(['reporter_id' => $reporter->id]);
        $this->actingAs($reporter)->post(route('reports.verify', $report->code), ['vote' => 'konfirmasi'])->assertSessionHasErrors('vote');

        $voter = User::factory()->create();
        $this->actingAs($voter)->post(route('reports.verify', $report->code), ['vote' => 'konfirmasi'])->assertRedirect();
        $this->actingAs($voter)->post(route('reports.verify', $report->code), ['vote' => 'konfirmasi'])->assertSessionHasErrors('vote');
    }

    public function test_petugas_can_move_report_through_the_valid_response_path(): void
    {
        $reporter = User::factory()->create();
        $petugas = User::factory()->create();
        $petugas->role = Role::Petugas;
        $petugas->save();
        $report = Report::factory()->create(['reporter_id' => $reporter->id, 'status' => 'terverifikasi']);

        $this->actingAs($petugas)->post(route('reports.status', $report->code), ['status' => 'diteruskan', 'note' => 'Diteruskan ke regu lapangan.'])->assertRedirect();
        $this->assertSame('diteruskan', $report->fresh()->status->value);
        $this->assertDatabaseHas('response_logs', ['report_id' => $report->id, 'actor_id' => $petugas->id, 'to_status' => 'diteruskan']);
        $this->assertSame(1, $reporter->fresh()->notifications()->count());
    }

    public function test_verified_report_can_complete_the_full_officer_response_lifecycle(): void
    {
        $reporter = User::factory()->create();
        $report = Report::factory()->create(['reporter_id' => $reporter->id]);
        foreach ([User::factory()->create(), User::factory()->create()] as $voter) {
            $this->actingAs($voter)->post(route('reports.verify', $report->code), ['vote' => 'konfirmasi'])->assertRedirect();
        }
        $this->assertSame('terverifikasi', $report->fresh()->status->value);

        $petugas = User::factory()->create();
        $petugas->role = Role::Petugas;
        $petugas->save();
        foreach ([['diteruskan', 'Diteruskan ke regu lapangan.'], ['dalam_penanganan', 'Tim mulai menangani.'], ['terkendali', 'Situasi terkendali.'], ['selesai', 'Penanganan selesai.']] as [$status, $note]) {
            $this->actingAs($petugas)->post(route('reports.status', $report->code), ['status' => $status, 'note' => $note])->assertRedirect();
        }

        $this->assertSame('selesai', $report->fresh()->status->value);
        $this->assertDatabaseHas('response_logs', ['report_id' => $report->id, 'to_status' => 'selesai']);
        $this->assertSame(5, $report->logs()->count());
        $this->assertGreaterThanOrEqual(2, $reporter->fresh()->notifications()->count());
    }

    public function test_photo_duplicate_and_approved_volunteer_can_complete_the_full_response_flow(): void
    {
        $reporter = User::factory()->create();
        $payload = ['lat' => -3.2, 'lng' => 105.3, 'land_type' => 'gambut', 'signs' => ['asap'], 'description' => 'Asap terlihat dari jalan.', 'photos' => [UploadedFile::fake()->image('bukti.png', 400, 300)]];
        $this->actingAs($reporter)->post(route('reports.store'), $payload)->assertRedirect();
        $report = Report::where('reporter_id', $reporter->id)->firstOrFail();
        $this->assertSame('image/jpeg', $report->photos()->firstOrFail()->mime);

        $duplicateReporter = User::factory()->create();
        $this->actingAs($duplicateReporter)->post(route('reports.store'), array_merge($payload, ['lat' => -3.201, 'lng' => 105.301, 'photos' => []]))->assertRedirect();
        $duplicate = Report::where('parent_id', $report->id)->firstOrFail();
        $this->assertSame('duplikat', $duplicate->status->value);
        $this->assertDatabaseHas('verifications', ['report_id' => $report->id, 'user_id' => $duplicateReporter->id, 'source_report_id' => $duplicate->id, 'vote' => 'konfirmasi']);

        $volunteer = User::factory()->create();
        $volunteer->role = Role::Relawan;
        $volunteer->volunteer_status = 'approved';
        $volunteer->save();
        $this->actingAs($volunteer)->post(route('reports.verify', $report->code), ['vote' => 'konfirmasi'])->assertRedirect();
        $this->assertSame('terverifikasi', $report->fresh()->status->value);

        $petugas = User::factory()->create();
        $petugas->role = Role::Petugas;
        $petugas->save();
        foreach ([['diteruskan', 'Diteruskan ke regu lapangan.'], ['dalam_penanganan', 'Tim mulai menangani.'], ['terkendali', 'Situasi terkendali.'], ['selesai', 'Penanganan selesai.']] as [$status, $note]) {
            $this->actingAs($petugas)->post(route('reports.status', $report->code), ['status' => $status, 'note' => $note])->assertRedirect();
        }

        $this->assertSame('selesai', $report->fresh()->status->value);
        $this->assertSame(5, $report->logs()->count());
        $this->assertGreaterThanOrEqual(5, $reporter->fresh()->notifications()->count());
    }
}
