<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'view_dashboard', 'description' => 'Melihat dashboard'],
            ['name' => 'view_members', 'description' => 'Melihat data jemaat'],
            ['name' => 'create_members', 'description' => 'Menambah data jemaat'],
            ['name' => 'edit_members', 'description' => 'Mengubah data jemaat'],
            ['name' => 'delete_members', 'description' => 'Menghapus data jemaat'],
            ['name' => 'export_members', 'description' => 'Ekspor data jemaat'],
            ['name' => 'import_members', 'description' => 'Impor data jemaat'],
            ['name' => 'view_categories', 'description' => 'Melihat kategori'],
            ['name' => 'create_categories', 'description' => 'Menambah kategori'],
            ['name' => 'edit_categories', 'description' => 'Mengubah kategori'],
            ['name' => 'delete_categories', 'description' => 'Menghapus kategori'],
            ['name' => 'view_users', 'description' => 'Melihat pengguna'],
            ['name' => 'create_users', 'description' => 'Menambah pengguna'],
            ['name' => 'edit_users', 'description' => 'Mengubah pengguna'],
            ['name' => 'delete_users', 'description' => 'Menghapus pengguna'],
            ['name' => 'assign_roles', 'description' => 'Menetapkan role pengguna'],
            ['name' => 'view_reports', 'description' => 'Melihat laporan'],
            ['name' => 'export_reports', 'description' => 'Ekspor laporan'],
            ['name' => 'view_settings', 'description' => 'Melihat pengaturan'],
            ['name' => 'edit_settings', 'description' => 'Mengubah pengaturan'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
