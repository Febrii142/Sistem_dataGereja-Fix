<?php

namespace Tests\Feature;

use App\Exports\MembersExport;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class MemberFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_index_supports_combined_category_filters(): void
    {
        $user = User::factory()->create();

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
        $user = User::factory()->create();

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
}
