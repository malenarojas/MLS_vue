<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RemaxTitlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Obtener el ID de "No seleccionado" en user_types (si aplica)
         $defaultUserTypeId = DB::table('user_types')->where('name', 'Agent')->value('id') ?? 1;

         // Lista de títulos
         $remaxTitles = [
             ["name" => "No seleccionado", "user_type_id" => $defaultUserTypeId],
             ["name" => "Agente Asociado", "user_type_id" => $defaultUserTypeId],
             ["name" => "Asistente con Licencia", "user_type_id" => $defaultUserTypeId],
             ["name" => "Gerente", "user_type_id" => $defaultUserTypeId],
             ["name" => "Propietario", "user_type_id" => $defaultUserTypeId],
             ["name" => "Administration Staff", "user_type_id" => $defaultUserTypeId],
             ["name" => "CMN Administrator", "user_type_id" => $defaultUserTypeId],
             ["name" => "Customer Care Manager", "user_type_id" => $defaultUserTypeId],
             ["name" => "Director of First Impression", "user_type_id" => $defaultUserTypeId],
             ["name" => "Financial Advisor", "user_type_id" => $defaultUserTypeId],
             ["name" => "Investing Partner", "user_type_id" => $defaultUserTypeId],
             ["name" => "Lettings Advisor", "user_type_id" => $defaultUserTypeId],
             ["name" => "Marketing Manager", "user_type_id" => $defaultUserTypeId],
             ["name" => "Mortgage Advisor", "user_type_id" => $defaultUserTypeId],
             ["name" => "Operating Partner", "user_type_id" => $defaultUserTypeId],
             ["name" => "Rental Manager", "user_type_id" => $defaultUserTypeId],
             ["name" => "Team Leader", "user_type_id" => $defaultUserTypeId],
             ["name" => "Team Manager", "user_type_id" => $defaultUserTypeId],
         ];

         // Insertar los títulos en la tabla
         foreach ($remaxTitles as &$title) {
             $title['created_at'] = now();
             $title['updated_at'] = now();
         }

         DB::table('remax_titles')->insert($remaxTitles);

    }
}
