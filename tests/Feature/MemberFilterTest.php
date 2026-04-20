<?php

namespace Tests\Feature;

use App\Exports\MembersExport;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class MemberFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_index_supports_combined_category_filters(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
        ]);

        Member::create([
            'nama' => 'Andi Dewasa',
            'alamat' => 'Alamat A',
            'kontak' => '0811',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Guru',
        ]);

        Member::create([
            'nama' => 'Bunga Dewasa',
            'alamat' => 'Alamat B',
            'kontak' => '0812',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Dosen',
        ]);

        Member::create([
            'nama' => 'Citra Anak',
            'alamat' => 'Alamat C',
            'kontak' => '0813',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(10)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => null,
        ]);

        Member::create([
            'nama' => 'Dimas Non Aktif',
            'alamat' => 'Alamat D',
            'kontak' => '0814',
            'status' => 'tidak_aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => null,
        ]);

        $response = $this->actingAs($user)->get(route('members.index', [
            'status' => 'aktif',
            'gender' => 'L',
            'age_category' => 'dewasa',
        ]));

        $response->assertOk();
        $response->assertSee('Andi Dewasa');
        $response->assertDontSee('Bunga Dewasa');
        $response->assertDontSee('Citra Anak');
        $response->assertDontSee('Dimas Non Aktif');
    }

    public function test_members_export_excel_uses_active_category_filters(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
        ]);

        Member::create([
            'nama' => 'Andi Dewasa',
            'alamat' => 'Alamat A',
            'kontak' => '0811',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Guru',
        ]);

        Member::create([
            'nama' => 'Bunga Dewasa',
            'alamat' => 'Alamat B',
            'kontak' => '0812',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Dosen',
        ]);

        Excel::fake();

        $this->actingAs($user)->get(route('members.export.excel', [
            'gender' => 'L',
            'age_category' => 'dewasa',
        ]));

        Excel::assertDownloaded('data-jemaat.xlsx', function (MembersExport $export) {
            return $export->collection()->pluck('nama')->all() === ['Andi Dewasa'];
        });
    }

    public function test_members_index_and_export_support_year_filter(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Staff')->value('id'),
            'role' => 'koordinator',
        ]);

        $member2025 = Member::create([
            'nama' => 'Jemaat Tahun 2025',
            'alamat' => 'Alamat 2025',
            'kontak' => '0815',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Guru',
        ]);

        $member2024 = Member::create([
            'nama' => 'Jemaat Tahun 2024',
            'alamat' => 'Alamat 2024',
            'kontak' => '0816',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(31)->toDateString(),
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Dosen',
        ]);

        Member::query()->whereKey($member2025->id)->update([
            'created_at' => now()->year(2025)->month(5)->day(10),
            'updated_at' => now()->year(2025)->month(5)->day(10),
        ]);

        Member::query()->whereKey($member2024->id)->update([
            'created_at' => now()->year(2024)->month(7)->day(15),
            'updated_at' => now()->year(2024)->month(7)->day(15),
        ]);

        $response = $this->actingAs($user)->get(route('members.index', [
            'year' => '2025',
        ]));

        $response->assertOk();
        $response->assertSee('Jemaat Tahun 2025');
        $response->assertDontSee('Jemaat Tahun 2024');

        Excel::fake();

        $this->actingAs($user)->get(route('members.export.excel', [
            'year' => '2024',
        ]));

        Excel::assertDownloaded('data-jemaat.xlsx', function (MembersExport $export) {
            return $export->collection()->pluck('nama')->all() === ['Jemaat Tahun 2024'];
        });
    }
}
