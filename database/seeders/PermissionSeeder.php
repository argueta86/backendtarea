<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create_role',
            'edit_role',
            'delete_role',
            'view_role',
            'create_permission',
            'edit_permission',
            'delete_permission',
            'view_permission',
            'create_user',
            'edit_user',
            'delete_user',
            'view_user',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web', // O 'api', según lo estés usando
            ]);
        }
    }
}
