<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_area_rejects_guests_and_regular_users(): void
    {
        $this->get('/petugas')->assertRedirect('/masuk');

        $warga = User::factory()->create();
        $this->actingAs($warga)->get('/petugas')->assertForbidden();
    }

    public function test_only_admin_can_open_admin_area(): void
    {
        $warga = User::factory()->create();
        $this->actingAs($warga)->get('/admin')->assertForbidden();

        $admin = User::factory()->create();
        $admin->role = Role::Admin;
        $admin->save();
        $this->assertSame('admin', $admin->fresh()->getRawOriginal('role'));
        $this->assertTrue($admin->fresh()->is_active);
        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_user_policy_prevents_editing_another_users_profile(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $this->assertTrue($owner->can('update', $owner));
        $this->assertFalse($owner->can('update', $other));
    }

    public function test_inactive_accounts_cannot_use_existing_sessions(): void
    {
        $user = User::factory()->create();
        $user->is_active = false;
        $user->save();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_public_registration_does_not_accept_a_role(): void
    {
        $this->post('/daftar', [
            'name' => 'Warga Baru', 'email' => 'warga@example.test',
            'password' => 'password123', 'password_confirmation' => 'password123', 'role' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->assertSame('warga', User::where('email', 'warga@example.test')->firstOrFail()->role->value);
    }
}
