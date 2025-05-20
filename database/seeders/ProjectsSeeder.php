<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            'No seleccionado',
            'Berchatti Norte',
            'Berchatti Norte II',
            'Bosques Guembe',
            'Condominio Mejia - Torre 3',
            'CONDOMINIO MEJIA - TORRE 4',
            'Domus Deluxe',
            'Domus Madero',
            'Edificio Valeria',
            'Irala Confort',
            'Magnolia',
            'OGA - VERTICAL HOMES',
            'Saota Loft - Equipetrol',
            'STONE By Portobello',
            'Torre Barcelona',
            'Trivento II',
            'Ziri Zwei',
            'Diagnósticos Médicos Integrales DMI'
        ];

        foreach ($projects as $projectName) {
            Project::create(['name' => $projectName]);
        }
    }
}
