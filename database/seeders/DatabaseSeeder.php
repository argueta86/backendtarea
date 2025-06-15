<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 🔻 Desactiva las restricciones de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Ejecuta los seeders
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            TareaSeeder::class,
        ]);

        // 🔺 Reactiva las restricciones
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
