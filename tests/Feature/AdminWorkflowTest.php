<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\PriorityConfig;
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
}
