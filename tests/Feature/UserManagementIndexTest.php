<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_only_shows_approved_admin_and_staff_users(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $admin = User::factory()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'role_id' => Role::query()->where('name', 'Admin')->value('id'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        User::factory()->create([
            'name' => 'Staff Operasional',
            'email' => 'staff@example.com',
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
            'status' => 'approved',
        ]);

        User::factory()->create([
            'name' => 'Jemaat Tidak Tampil',
            'email' => 'jemaat@example.com',
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'role' => 'jemaat',
            'status' => 'approved',
        ]);

        User::factory()->create([
            'name' => 'Staff Pending',
            'email' => 'staff-pending@example.com',
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('users.index'));

        $response
            ->assertOk()
            ->assertSeeText('Admin Utama')
            ->assertSeeText('Staff Operasional')
            ->assertDontSeeText('Jemaat Tidak Tampil')
            ->assertDontSeeText('Staff Pending')
            ->assertViewHas('totalUsers', 2)
            ->assertViewHas('adminRolesCount', 1);
    }

    public function test_user_management_supports_name_or_email_search_for_staff_directory(): void
    {
        $this->seed([PermissionSeeder::class, RoleSeeder::class]);

        $admin = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Admin')->value('id'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        User::factory()->create([
            'name' => 'Koordinator Ibadah',
            'email' => 'koordinator@example.com',
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
            'status' => 'approved',
        ]);

        User::factory()->create([
            'name' => 'Staff Media',
            'email' => 'media@example.com',
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($admin)->get(route('users.index', ['search' => 'koordinator@example.com']));

        $response
            ->assertOk()
            ->assertSeeText('Koordinator Ibadah')
            ->assertDontSeeText('Staff Media');
    }
}
