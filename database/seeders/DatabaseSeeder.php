<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Jemaat;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
        ]);

        $adminRoleId = Role::query()->where('name', 'Admin')->value('id');
        $jemaatRoleId = Role::query()->where('name', 'Jemaat Gereja')->value('id')
            ?? Role::query()->where('name', 'Member')->value('id');

        User::query()->updateOrCreate(
            ['email' => 'admin@gereja.local'],
            [
                'name' => 'Admin Gereja',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'role_id' => $adminRoleId,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'jemaat@gereja.local'],
            [
                'name' => 'Jemaat Gereja',
                'password' => Hash::make('Jemaat123!'),
                'role' => 'jemaat',
                'role_id' => $jemaatRoleId,
            ]
        );

        if (Member::count() === 0) {
            $members = collect([
                [
                    'nama' => 'Yohanes Sitorus',
                    'alamat' => 'Jl. Kasih No. 10',
                    'kontak' => '081234567890',
                    'status' => 'aktif',
                    'tanggal_lahir' => '1990-05-12',
                    'jenis_kelamin' => 'L',
                    'pekerjaan' => 'Guru',
                ],
                [
                    'nama' => 'Maria Simanjuntak',
                    'alamat' => 'Jl. Damai No. 2',
                    'kontak' => '081298765432',
                    'status' => 'aktif',
                    'tanggal_lahir' => '1988-09-21',
                    'jenis_kelamin' => 'P',
                    'pekerjaan' => 'Perawat',
                ],
            ])->map(fn (array $member) => Member::create($member));

            $members->each(function (Member $member) {
                Attendance::create([
                    'member_id' => $member->id,
                    'service_date' => now()->toDateString(),
                    'hadir' => true,
                    'keterangan' => 'Ibadah Minggu',
                ]);
            });
        }

        if (Jemaat::count() === 0) {
            $sampleJemaat = Jemaat::query()->create([
                'user_id' => User::query()->where('email', 'jemaat@gereja.local')->value('id'),
                'nama_lengkap' => 'Samuel Panjaitan',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1992-04-12',
                'alamat' => 'Jl. Sukacita No. 9',
                'kota' => 'Medan',
                'kode_pos' => '20111',
                'no_telepon' => '081355501234',
                'email' => 'samuel.panjaitan@gereja.local',
                'status_perkawinan' => 'Menikah',
                'kategori_jemaat' => 'Dewasa',
                'status_baptis' => 'sudah',
                'tanggal_baptis' => '2008-07-20',
            ]);

            $categoryIds = DB::table('categories')
                ->whereIn('name', ['Dewasa', 'Menikah', 'Medan'])
                ->pluck('id');

            foreach ($categoryIds as $categoryId) {
                DB::table('jemaat_categories')->insert([
                    'jemaat_id' => $sampleJemaat->id,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
