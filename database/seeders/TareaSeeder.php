<?php

namespace Database\Seeders;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    public function run(): void
    {
        // Asegúrate de tener al menos un usuario
        $user = User::first();

        if (!$user) {
            $this->command->info('No hay usuarios para asignar tareas.');
            return;
        }

        for ($i = 1; $i <= 10; $i++) {
            Tarea::create([
                'user_id'     => $user->id,
                'titulo'      => "Tarea de prueba #$i",
                'descripcion' => "Descripción para la tarea de prueba número $i.",
                'completada'  => $i % 2 === 0, 
            ]);
        }

        $this->command->info('Tareas de prueba creadas exitosamente.');
    }
}
