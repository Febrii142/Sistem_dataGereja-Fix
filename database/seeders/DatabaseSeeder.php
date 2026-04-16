<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);

        $adminRoleId = Role::query()->where('name', 'Admin')->value('id');

        User::query()->updateOrCreate(
            ['email' => 'admin@gereja.local'],
            [
                'name' => 'Admin Gereja',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'role_id' => $adminRoleId,
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
    }
}
