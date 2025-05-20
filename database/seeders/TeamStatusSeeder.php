<?php

namespace Database\Seeders;

use App\Models\TeamStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            'Administrador de equipo',
            'Individual',
            'Miembro de Equipo'
        ];

        foreach ($estados as $estado) {
            TeamStatus::create(['name' => $estado]);
        }
    }
}
