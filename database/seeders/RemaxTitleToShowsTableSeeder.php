<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemaxTitleToShowsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Lista de títulos para mostrar
        $remaxTitlesToShow = [
            ["name" => "No seleccionado"],
            ["name" => "Agente Asociado"],
            ["name" => "Agente en Entrenamiento"],
            ["name" => "Asesor de Alquileres"],
            ["name" => "Asesor Financiero"],
            ["name" => "Asesor Hipotecario"],
            ["name" => "Asistente con Licencia"],
            ["name" => "Broker Manager"],
            ["name" => "Broker/Owner"],
            ["name" => "Director of First Impression"],
            ["name" => "Gerente de Atención al Cliente"],
            ["name" => "Gerente de Equipo"],
            ["name" => "Gerente de Marketing"],
            ["name" => "Investing Partner"],
            ["name" => "Operating Partner"],
            ["name" => "Staff de Oficina"],
            ["name" => "Team Leader"],
        ];

        // Agregar timestamps a cada título
        foreach ($remaxTitlesToShow as &$title) {
            $title['created_at'] = now();
            $title['updated_at'] = now();
        }

        // Insertar los títulos en la tabla
        DB::table('remax_title_to_shows')->insert($remaxTitlesToShow);

    }
}
