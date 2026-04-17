<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin' => [
                'description' => 'Memiliki semua permission sistem',
                'permissions' => Permission::query()->pluck('name')->all(),
            ],
            'Pendeta' => [
                'description' => 'Dapat melihat dashboard, jemaat, kategori, dan laporan',
                'permissions' => ['view_members', 'view_categories', 'view_reports', 'view_dashboard'],
            ],
            'Staff' => [
                'description' => 'Dapat mengelola data jemaat sesuai tugas operasional',
                'permissions' => [
                    'view_members',
                    'create_members',
                    'edit_members',
                    'export_members',
                    'view_categories',
                    'view_dashboard',
                ],
            ],
            'Jemaat Gereja' => [
                'description' => 'Akses dashboard jemaat, profil pribadi, dan manajemen keluarga',
                'permissions' => [
                    'view_jemaat_dashboard',
                    'edit_own_jemaat',
                    'view_own_family',
                    'manage_family_members',
                ],
            ],
            'Member' => [
                'description' => 'Akses terbatas hanya dashboard pribadi',
                'permissions' => ['view_dashboard'],
            ],
        ];

        foreach ($roles as $roleName => $config) {
            $role = Role::query()->updateOrCreate(
                ['name' => $roleName],
                ['description' => $config['description']]
            );

            $permissionIds = Permission::query()
                ->whereIn('name', $config['permissions'])
                ->pluck('id')
                ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
