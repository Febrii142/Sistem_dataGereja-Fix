<?php

namespace Database\Seeders;

use App\Models\KategoriJemaat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $kategoriJemaat = [
            ['nama' => 'Umum', 'deskripsi' => 'Kategori umum jemaat'],
            ['nama' => 'Remaja', 'deskripsi' => 'Kategori usia remaja'],
            ['nama' => 'Dewasa', 'deskripsi' => 'Kategori usia dewasa'],
            ['nama' => 'Lansia', 'deskripsi' => 'Kategori usia lansia'],
        ];

        foreach ($kategoriJemaat as $kategori) {
            KategoriJemaat::query()->updateOrCreate(
                ['nama' => $kategori['nama']],
                ['deskripsi' => $kategori['deskripsi']]
            );
        }

        $categories = [
            ['type' => 'age', 'name' => 'Bayi', 'description' => '0-3 tahun'],
            ['type' => 'age', 'name' => 'Anak', 'description' => '4-12 tahun'],
            ['type' => 'age', 'name' => 'Remaja', 'description' => '13-18 tahun'],
            ['type' => 'age', 'name' => 'Dewasa', 'description' => '19-59 tahun'],
            ['type' => 'age', 'name' => 'Lansia', 'description' => '60+ tahun'],
            ['type' => 'status', 'name' => 'Belum Menikah', 'description' => 'Status perkawinan belum menikah'],
            ['type' => 'status', 'name' => 'Menikah', 'description' => 'Status perkawinan menikah'],
            ['type' => 'status', 'name' => 'Janda', 'description' => 'Status perkawinan janda'],
            ['type' => 'status', 'name' => 'Duda', 'description' => 'Status perkawinan duda'],
            ['type' => 'region', 'name' => 'Medan', 'description' => 'Wilayah pelayanan Medan'],
            ['type' => 'region', 'name' => 'Jakarta', 'description' => 'Wilayah pelayanan Jakarta'],
            ['type' => 'region', 'name' => 'Bandung', 'description' => 'Wilayah pelayanan Bandung'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->updateOrInsert(
                ['type' => $category['type'], 'name' => $category['name']],
                [
                    'description' => $category['description'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
