<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_page_applies_demographic_filters_and_shows_new_sections(): void
    {
        $this->seed([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $user = User::factory()->create([
            'role_id' => Role::query()->where('name', 'Pendeta')->value('id'),
            'role' => 'pendeta',
        ]);

        Member::create([
            'nama' => 'Lia Dewasa April',
            'alamat' => 'Alamat A',
            'kontak' => '0801',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(25)->month(4)->day(12)->toDateString(),
            'jenis_kelamin' => 'P',
            'pekerjaan' => 'Guru',
        ]);

        Member::create([
            'nama' => 'Budi Dewasa Mei',
            'alamat' => 'Alamat B',
            'kontak' => '0802',
            'status' => 'aktif',
            'tanggal_lahir' => now()->subYears(30)->month(5)->day(2)->toDateString(),
            'jenis_kelamin' => 'L',
            'pekerjaan' => 'Wiraswasta',
        ]);

        $response = $this->actingAs($user)->get(route('reports.index', [
            'age_range' => 'dewasa',
            'gender' => 'P',
            'birthday_month' => '4',
        ]));

        $response->assertOk();
        $response->assertSee('ANALISIS DEMOGRAFI');
        $response->assertSee('Distribusi Sektor');
        $response->assertSee('Lia Dewasa April');
        $response->assertDontSee('Budi Dewasa Mei');
    }
}
