<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DocumentacionTypeAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el tipo principal para Documentación Privada
        $DocumentacionAgent = DB::table('documentation_types')->insertGetId([
            'name' => 'Documentación De Agente',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subtipos para Documentación Privada
        $documentacionagentSubtypes = [
            'No seleccionado',
            'Celula de Identidad',
            'tipo de licencia',
        ];

        foreach ($documentacionagentSubtypes as $subtype) {
            DB::table('documentation_types')->insert([
                'name' => $subtype,
                'parent_id' => $DocumentacionAgent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
