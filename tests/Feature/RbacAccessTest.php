<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function createUserWithRole(string $roleName): User
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        return User::factory()->create([
            'role_id' => Role::query()->where('name', $roleName)->value('id'),
            'role' => match ($roleName) {
                'Admin' => 'admin',
                'Pendeta' => 'pendeta',
                'Staff' => 'koordinator',
                'Jemaat Gereja' => 'jemaat',
                default => 'user',
            },
        ]);
    }

    public function test_admin_can_access_user_and_role_management_pages(): void
    {
        $admin = $this->createUserWithRole('Admin');

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('roles.index'), false);
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
        $this->actingAs($admin)->get(route('roles.index'))->assertOk();
    }

    public function test_pendeta_can_view_reports_but_cannot_manage_users(): void
    {
        $pendeta = $this->createUserWithRole('Pendeta');

        $this->actingAs($pendeta)->get(route('reports.index'))->assertOk();
        $this->actingAs($pendeta)->get(route('users.index'))->assertForbidden();
    }

    public function test_member_only_has_dashboard_access_from_rbac_protected_routes(): void
    {
        $member = $this->createUserWithRole('Jemaat Gereja');
        $admin = $this->createUserWithRole('Admin');

        $this->actingAs($member)
            ->get(route('jemaat.dashboard'))
            ->assertOk()
            ->assertSee(route('jemaat.dashboard'), false)
            ->assertSee(route('jemaat.profile'), false)
            ->assertSee(route('jemaat.keluarga.index'), false)
            ->assertSee(route('jemaat.profile.edit'), false)
            ->assertDontSee(route('categories.index'), false)
            ->assertDontSee(route('users.index'), false)
            ->assertDontSee(route('roles.index'), false)
            ->assertDontSee(route('reports.index'), false);
        $this->actingAs($member)->get('/jemaat/profile')->assertOk();
        $this->actingAs($member)->get('/jemaat/family')->assertOk();
        $this->actingAs($member)->get(route('members.index'))->assertForbidden();
        $this->actingAs($member)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($admin)->get('/jemaat/dashboard')->assertForbidden();
    }
}
