<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que los roles existan con el guard correcto
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // Crear usuario Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'Ana@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Ana12345'),
            ]
        );
        $superAdmin->assignRole($superAdminRole);

        // Crear usuario Admin
        $admin = User::firstOrCreate(
            ['email' => 'beatriz@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin1234'),
            ]
        );
        $admin->assignRole($adminRole);
        
        $userRole = Role::firstOrCreate([
        'name' => 'User',
        'guard_name' => 'web',
        ]);

        
    }
}
