<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
    }

    public function test_authenticated_user_can_create_category(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'Wilayah Barat',
            'type' => 'wilayah',
            'description' => 'Kelompok wilayah barat',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Wilayah Barat',
            'type' => 'wilayah',
        ]);
    }

    public function test_member_can_be_assigned_to_multiple_categories(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $categoryA = Category::create(['name' => 'Remaja (13-18)', 'type' => 'umur', 'min_age' => 13, 'max_age' => 18]);
        $categoryB = Category::create(['name' => 'Aktif', 'type' => 'status']);

        $response = $this->actingAs($user)->post(route('members.store'), [
            'nama' => 'Jemaat Uji',
            'alamat' => 'Jl. Uji No. 1',
            'kontak' => '081111111111',
            'status' => 'aktif',
            'tanggal_lahir' => '2010-01-01',
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Pelajar',
            'category_ids' => [$categoryA->id, $categoryB->id],
        ]);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseCount('jemaat_categories', 2);
    }

    public function test_members_index_can_be_filtered_by_category(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['name' => 'Wilayah Timur', 'type' => 'wilayah']);

        $this->actingAs($user)->post(route('members.store'), [
            'nama' => 'Jemaat Tersaring',
            'alamat' => 'Jl. Timur No. 1',
            'kontak' => '082222222222',
            'status' => 'aktif',
            'tanggal_lahir' => '1995-01-01',
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Karyawan',
            'category_ids' => [$category->id],
        ]);

        $this->actingAs($user)->post(route('members.store'), [
            'nama' => 'Jemaat Lain',
            'alamat' => 'Jl. Lain No. 2',
            'kontak' => '083333333333',
            'status' => 'aktif',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Guru',
        ]);

        $response = $this->actingAs($user)->get(route('members.index', ['category_id' => $category->id]));
        $response->assertOk();
        $response->assertSee('Jemaat Tersaring');
        $response->assertDontSee('Jemaat Lain');
    }

    public function test_members_index_can_be_filtered_by_join_year(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $member2024 = Member::create([
            'nama' => 'Jemaat Tahun 2024',
            'alamat' => 'Jl. A',
            'kontak' => '081234567890',
            'status' => 'aktif',
            'tanggal_lahir' => '1990-01-01',
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Karyawan',
        ]);
        $member2024->forceFill(['created_at' => '2024-05-10 10:00:00'])->save();

        $member2023 = Member::create([
            'nama' => 'Jemaat Tahun 2023',
            'alamat' => 'Jl. B',
            'kontak' => '081234567891',
            'status' => 'aktif',
            'tanggal_lahir' => '1992-01-01',
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Guru',
        ]);
        $member2023->forceFill(['created_at' => '2023-07-11 10:00:00'])->save();

        $response = $this->actingAs($user)->get(route('members.index', ['tahun_bergabung' => 2024]));

        $response->assertOk();
        $response->assertSee('Jemaat Tahun 2024');
        $response->assertDontSee('Jemaat Tahun 2023');
    }
}
