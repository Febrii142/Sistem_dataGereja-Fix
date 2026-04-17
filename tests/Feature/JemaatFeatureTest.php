<?php

namespace Tests\Feature;

use App\Models\Jemaat;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JemaatFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createJemaatUser(string $name = 'Jemaat Test'): User
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        return User::factory()->create([
            'name' => $name,
            'email' => strtolower(str_replace(' ', '', $name)).'@gereja.test',
            'role_id' => Role::query()->where('name', 'Jemaat Gereja')->value('id'),
            'role' => 'jemaat',
        ]);
    }

    public function test_jemaat_can_complete_registration_flow_with_validation(): void
    {
        $user = $this->createJemaatUser();

        $this->actingAs($user)
            ->post(route('jemaat.registration.save', 1), [
                'nama_lengkap' => 'Ab',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1999-01-01',
                'no_telepon' => '08123456789',
                'email' => 'jemaat@test.com',
            ])
            ->assertSessionHasErrors(['nama_lengkap']);

        $this->actingAs($user)
            ->post(route('jemaat.registration.save', 1), [
                'nama_lengkap' => 'Abraham Simbolon',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1999-01-01',
                'no_telepon' => '08123456789',
                'email' => 'jemaat@test.com',
            ])
            ->assertRedirect(route('jemaat.registration.show', 2));

        $this->actingAs($user)
            ->post(route('jemaat.registration.save', 2), [
                'alamat' => 'Jl. Damai No. 17',
                'kota' => 'Bandung',
                'kode_pos' => '40123',
            ])
            ->assertRedirect(route('jemaat.registration.show', 3));

        $this->actingAs($user)
            ->post(route('jemaat.registration.save', 3), [
                'status_baptis' => 'sudah',
            ])
            ->assertSessionHasErrors(['tanggal_baptis', 'tempat_baptis']);

        $this->actingAs($user)
            ->post(route('jemaat.registration.save', 3), [
                'status_baptis' => 'sudah',
                'tanggal_baptis' => '2021-01-10',
                'tempat_baptis' => 'Gereja Pusat',
                'nama_pendeta' => 'Pdt. Markus',
            ])
            ->assertRedirect(route('jemaat.dashboard'));

        $this->assertDatabaseHas('jemaat', [
            'user_id' => $user->id,
            'nama_lengkap' => 'Abraham Simbolon',
            'status_baptis' => 'sudah',
            'tanggal_baptis' => '2021-01-10',
        ]);

        $jemaatId = Jemaat::query()->where('user_id', $user->id)->value('id');

        $this->assertDatabaseHas('baptisan', [
            'jemaat_id' => $jemaatId,
            'tempat_baptis' => 'Gereja Pusat',
        ]);
    }

    public function test_only_kepala_keluarga_can_manage_family_members(): void
    {
        $kepalaUser = $this->createJemaatUser('Kepala Keluarga');
        $anggotaUser = $this->createJemaatUser('Anggota Keluarga');

        $kepala = Jemaat::query()->create([
            'user_id' => $kepalaUser->id,
            'nama_lengkap' => 'Kepala Keluarga',
            'tempat_lahir' => 'Medan',
            'tanggal_lahir' => '1985-01-01',
            'no_telepon' => '08110000001',
            'email' => 'kepala@gereja.test',
        ]);

        Jemaat::query()->create([
            'user_id' => $anggotaUser->id,
            'nama_lengkap' => 'Anggota Keluarga',
            'tempat_lahir' => 'Medan',
            'tanggal_lahir' => '2010-01-01',
            'no_telepon' => '08110000002',
            'email' => 'anggota@gereja.test',
            'kepala_keluarga_id' => $kepala->id,
        ]);

        $this->actingAs($anggotaUser)
            ->get(route('jemaat.keluarga.create'))
            ->assertForbidden();

        $this->actingAs($kepalaUser)
            ->post(route('jemaat.keluarga.store'), [
                'mode' => 'new',
                'nama_lengkap' => 'Anak Baru',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '2015-05-05',
                'no_telepon' => '08110000003',
                'email' => 'anakbaru@gereja.test',
                'hubungan_keluarga' => 'Anak',
                'status' => 'aktif',
            ])
            ->assertRedirect(route('jemaat.keluarga.index'));

        $this->assertDatabaseHas('anggota_keluarga', [
            'kepala_keluarga_id' => $kepala->id,
            'hubungan_keluarga' => 'Anak',
        ]);
    }
}
