<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
