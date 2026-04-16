<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bayi (0-3)', 'type' => 'umur', 'min_age' => 0, 'max_age' => 3],
            ['name' => 'Anak (4-12)', 'type' => 'umur', 'min_age' => 4, 'max_age' => 12],
            ['name' => 'Remaja (13-18)', 'type' => 'umur', 'min_age' => 13, 'max_age' => 18],
            ['name' => 'Dewasa (19-55)', 'type' => 'umur', 'min_age' => 19, 'max_age' => 55],
            ['name' => 'Lansia (55+)', 'type' => 'umur', 'min_age' => 55, 'max_age' => null],
            ['name' => 'Aktif', 'type' => 'status'],
            ['name' => 'Non-aktif', 'type' => 'status'],
            ['name' => 'Pindah', 'type' => 'status'],
        ];

        foreach ($categories as $category) {
            Category::query()->updateOrCreate(
                [
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    'description' => $category['description'] ?? null,
                    'min_age' => $category['min_age'] ?? null,
                    'max_age' => $category['max_age'] ?? null,
                ]
            );
        }
    }
}
