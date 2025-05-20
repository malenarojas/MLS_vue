<?php

namespace Database\Seeders;

use App\Models\RemaxTitleToShow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemaxTitleToShowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $titulos = [
            'Agente Asociado',
            'Agente en Entrenamiento',
            'Asesor de Alquileres',
            'Asesor Financiero',
            'Asesor Hipotecario',
            'Asistente con Licencia',
            'Broker Manager',
            'Broker/Owner',
            'Director of First Impression',
            'Gerente de Atención al Cliente',
            'Gerente de Equipo',
            'Gerente de Marketing',
            'Investing Partner',
            'Operating Partner',
            'Staff de Oficina',
            'Team Leader'
        ];

        foreach ($titulos as $titulo) {
            RemaxTitleToShow::create(['name' => $titulo]);
        }
    }
}
