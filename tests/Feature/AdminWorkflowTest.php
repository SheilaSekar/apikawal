<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\PriorityConfig;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_open_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.index'))->assertForbidden();
        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();
        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
    }

    public function test_admin_can_change_weights_only_when_total_is_one_hundred(): void
    {
        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();
        $weights = config('apikawal.priority.weights');
        $this->actingAs($admin)->put(route('admin.weights.update'), ['weights' => array_replace($weights, ['w_photo' => 6])])->assertSessionHasErrors('weights');
        $valid = array_replace($weights, ['w_photo' => 6, 'w_support' => 9]);
        $this->put(route('admin.weights.update'), ['weights' => $valid])->assertRedirect();
        $this->assertSame(6.0, (float) PriorityConfig::where('name', 'w_photo')->value('value'));
    }

    public function test_admin_moderation_rejection_requires_a_reason_and_writes_a_response_log(): void
    {
        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();
        $report = Report::factory()->create(['needs_moderation' => true]);
        $this->actingAs($admin)->patch(route('admin.moderation.approve', $report), ['approved' => 0])->assertSessionHasErrors('reason');
        $this->patch(route('admin.moderation.approve', $report), ['approved' => 0, 'reason' => 'Bukti tidak memenuhi aturan moderasi.'])->assertRedirect();
        $this->assertSame('ditolak', $report->fresh()->status->value);
        $this->assertFalse($report->fresh()->needs_moderation);
        $this->assertDatabaseHas('response_logs', ['report_id' => $report->id, 'actor_id' => $admin->id, 'to_status' => 'ditolak']);
    }
}
