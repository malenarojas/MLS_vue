<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Comercial',
            'Educativo',
            'Residencial',
            'Industrial',
            'Oficinas',
            'Institucional y Salud',
            'Hospitalidad y Alojamiento',
            'Recreacional',
            'Agrícola / Ganadero',
            'Inversión y Desarrollo',
            'Otros',
        ];

        foreach ($types as $type) {
            DB::table('type_properties')->insert(['name' => $type]);
        }
    }
}
