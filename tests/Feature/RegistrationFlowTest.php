<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\NewRegistrationSubmittedNotification;
use App\Notifications\RegistrationCredentialsNotification;
use App\Notifications\RegistrationStatusUpdatedNotification;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }

    public function test_guest_can_submit_registration_form_and_account_created_pending(): void
    {
        Notification::fake();

        $staff = User::factory()->create([
            'role' => 'admin',
            'role_id' => Role::query()->where('name', 'Admin')->value('id'),
            'status' => 'approved',
        ]);

        $this->post(route('register.store'), [
            'name' => 'Jemaat Baru',
            'email' => 'jemaat.baru@test.local',
            'no_telepon' => '081212341234',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1998-02-10',
            'alamat' => 'Jl. Kebaktian No. 1',
            'kota' => 'Bandung',
            'kode_pos' => '40111',
            'baptism_status' => 'belum_dibaptis',
            'catechism_batch' => 'Gelombang 1 (Januari - April)',
            'parent_guardian_name' => 'Budi Santoso',
        ])->assertRedirect(route('register.success'))
            ->assertSessionHas('registration_success.email', 'jemaat.baru@test.local')
            ->assertSessionHas('registration_success.password');

        $this->assertDatabaseHas('users', [
            'email' => 'jemaat.baru@test.local',
            'status' => 'pending',
            'role' => 'jemaat',
        ]);
        $this->assertDatabaseHas('members', [
            'nama' => 'Jemaat Baru',
            'kontak' => '081212341234',
            'baptism_status' => 'belum_dibaptis',
            'catechism_batch' => 'Gelombang 1 (Januari - April)',
            'parent_guardian_name' => 'Budi Santoso',
        ]);
        $this->assertDatabaseHas('jemaat', [
            'nama_lengkap' => 'Jemaat Baru',
            'kelas_katekisasi' => 'Gelombang 1 (Januari - April)',
        ]);

        $newUser = User::query()->where('email', 'jemaat.baru@test.local')->firstOrFail();
        Notification::assertSentTo($newUser, RegistrationCredentialsNotification::class);
        Notification::assertSentTo($staff, NewRegistrationSubmittedNotification::class);
    }

    public function test_pending_user_gets_forbidden_for_profile_update(): void
    {
        $pendingUser = User::factory()->create([
            'role' => 'jemaat',
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'status' => 'pending',
        ]);

        $this->actingAs($pendingUser)->post(route('jemaat.profile.update'), [
            'nama' => 'Jemaat Pending',
            'tanggal_lahir' => '1990-01-01',
            'alamat' => 'Jl. Testing',
            'nomor_telepon' => '081200000000',
        ])->assertForbidden();
    }

    public function test_registration_still_succeeds_when_notification_dispatch_fails(): void
    {
        Notification::shouldReceive('send')
            ->twice()
            ->andThrow(new \RuntimeException('Too many emails per second'));

        $this->post(route('register.store'), [
            'name' => 'Jemaat Gagal Email',
            'email' => 'jemaat.gagal-email@test.local',
            'no_telepon' => '081211112222',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1998-02-10',
            'alamat' => 'Jl. Kebaktian No. 2',
            'kota' => 'Bandung',
            'kode_pos' => '40111',
            'baptism_status' => 'sudah_dibaptis',
            'baptism_date' => '2015-08-17',
            'baptism_location' => 'Gereja Pusat Bandung',
        ])->assertRedirect(route('register.success'))
            ->assertSessionHas('registration_success.email', 'jemaat.gagal-email@test.local')
            ->assertSessionHas('registration_success.password');

        $this->assertDatabaseHas('users', [
            'email' => 'jemaat.gagal-email@test.local',
            'status' => 'pending',
            'role' => 'jemaat',
        ]);
        $this->assertDatabaseHas('members', [
            'nama' => 'Jemaat Gagal Email',
            'baptism_status' => 'sudah_dibaptis',
            'baptism_location' => 'Gereja Pusat Bandung',
        ]);
    }

    public function test_guest_registration_requires_catechism_data_when_not_baptized(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Jemaat Katekisasi',
                'email' => 'jemaat.katekisasi@test.local',
                'no_telepon' => '081212349999',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-04-12',
                'alamat' => 'Jl. Kasih No. 8',
                'kota' => 'Jakarta',
                'kode_pos' => '12345',
                'baptism_status' => 'belum_dibaptis',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['catechism_batch', 'parent_guardian_name']);
    }

    public function test_staff_can_approve_pending_registration(): void
    {
        Notification::fake();

        $admin = User::factory()->create([
            'role' => 'admin',
            'role_id' => Role::query()->where('name', 'Admin')->value('id'),
            'status' => 'approved',
        ]);

        $pendingUser = User::factory()->create([
            'role' => 'jemaat',
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.registrations.approve', $pendingUser))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $pendingUser->id,
            'status' => 'approved',
        ]);
        Notification::assertSentTo($pendingUser->fresh(), RegistrationStatusUpdatedNotification::class);
    }

    public function test_registration_success_page_requires_temporary_session_data(): void
    {
        $this->get(route('register.success'))
            ->assertRedirect(route('register'))
            ->assertSessionHas('error');
    }

    public function test_registration_success_page_shows_credentials_and_clears_session_after_view(): void
    {
        $this->withSession([
            'registration_success' => [
                'email' => 'jemaat.success@test.local',
                'password' => 'TempPass123!',
            ],
        ])->get(route('register.success'))
            ->assertOk()
            ->assertSee('jemaat.success@test.local')
            ->assertSee('TempPass123!');

        $this->get(route('register.success'))
            ->assertRedirect(route('register'))
            ->assertSessionHas('error');
    }
}
