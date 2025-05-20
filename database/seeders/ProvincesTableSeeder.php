<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
             // Insertar provincias en la tabla 'provinces'
             DB::table('provinces')->insert([
                ['name' => 'Andrés Ibáñez', 'state_id' => 1],
                ['name' => 'Ángel Sandoval', 'state_id' => 1],
                ['name' => 'Chiquitos', 'state_id' => 1],
                ['name' => 'Cordillera', 'state_id' => 1],
                ['name' => 'Florida', 'state_id' => 1],
                ['name' => 'Germán Busch', 'state_id' => 1],
                ['name' => 'Guarayos', 'state_id' => 1],
                ['name' => 'Ichilo', 'state_id' => 1],
                ['name' => 'José Miguel de Velasco', 'state_id' => 1],
                ['name' => 'Manuel María Caballero', 'state_id' => 1],
                ['name' => 'Ñuflo de Chávez', 'state_id' => 1],
                ['name' => 'Obispo Santisteban', 'state_id' => 1],
                ['name' => 'Sara', 'state_id' => 1],
                ['name' => 'Vallegrande', 'state_id' => 1],
                ['name' => 'Warnes', 'state_id' => 1],
            ]);
    }
}
