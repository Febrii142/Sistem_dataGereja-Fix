<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CategorySeeder::class);

        User::query()->updateOrCreate(
            ['email' => 'admin@gereja.local'],
            [
                'name' => 'Admin Gereja',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
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

        $ageCategories = Category::query()
            ->where('type', 'umur')
            ->orderBy('min_age')
            ->get();
        $statusCategories = Category::query()
            ->where('type', 'status')
            ->get()
            ->keyBy(fn (Category $category) => strtolower($category->name));

        Member::query()->get()->each(function (Member $member) use ($ageCategories, $statusCategories) {
            $categoryIds = [];

            if ($member->tanggal_lahir) {
                $usia = Carbon::parse($member->tanggal_lahir)->age;
                $ageCategory = $ageCategories->first(function (Category $category) use ($usia) {
                    $minAge = $category->min_age ?? 0;
                    $maxAge = $category->max_age;

                    return $usia >= $minAge && ($maxAge === null || $usia <= $maxAge);
                });

                if ($ageCategory) {
                    $categoryIds[] = $ageCategory->id;
                }
            }

            $statusKey = $member->status === 'aktif' ? 'aktif' : 'non-aktif';
            if ($statusCategories->has($statusKey)) {
                $categoryIds[] = $statusCategories->get($statusKey)->id;
            }

            if ($categoryIds !== []) {
                $member->categories()->syncWithoutDetaching(array_unique($categoryIds));
            }
        });
    }
}
