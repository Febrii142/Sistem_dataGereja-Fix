<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberVerificationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_index_shows_verification_card_and_hides_export_actions(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $pendeta = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Pendeta')->value('id'),
            'role' => 'pendeta',
            'status' => 'approved',
        ]);

        Member::create([
            'nama' => 'Jemaat Satu',
            'alamat' => 'Alamat Uji',
            'kontak' => '081200000001',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'L',
        ]);

        User::factory()->count(2)->create([
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'role' => 'jemaat',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($pendeta)->get(route('members.index'));

        $response->assertOk();
        $response->assertSee('Verifikasi Jemaat Baru');
        $response->assertSee('Ada 2 pendaftaran baru');
        $response->assertSee('Cari nama, email, atau kontak...');
        $response->assertSee('Menampilkan 1 jemaat');
        $response->assertDontSee('Tambah Data Baru');
        $response->assertDontSee(route('members.export.pdf'));
        $response->assertDontSee(route('members.export.excel'));
    }

    public function test_verification_page_is_accessible_from_members_menu_and_has_actions(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $admin = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Admin')->value('id'),
            'role' => 'admin',
            'status' => 'approved',
        ]);

        $pendingUser = User::factory()->create([
            'name' => 'Pendaftar Pending',
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'role' => 'jemaat',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('members.verification.index'));

        $response->assertOk();
        $response->assertSee('Pendaftar Pending');
        $response->assertSee(route('members.verification.approve', $pendingUser), false);
        $response->assertSee(route('members.verification.reject', $pendingUser), false);
        $response->assertSee('Verifikasi');
        $response->assertSee('X');
    }
}
