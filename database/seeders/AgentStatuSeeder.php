<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AgentStatuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Insertar los estados en la tabla 'agent_status'
		  DB::table('agents_statuses')->insert([
            ['name' => 'Activo'],
            ['name' => 'Inactivo'],
            ['name' => 'Aprobado'],
            ['name' => 'Pendiente'],
            ['name' => 'Rechazado'],
        ]);
    }
}
