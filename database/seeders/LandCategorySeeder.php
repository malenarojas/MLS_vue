<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landCategories = [
            ['land_category_name' => 'No seleccionado'],
            ['land_category_name' => 'Área de minería de primavera'],
            ['land_category_name' => 'Área de Precinto'],
            ['land_category_name' => 'Bien'],
            ['land_category_name' => 'Bosque protegido'],
            ['land_category_name' => 'Caminos públicos'],
            ['land_category_name' => 'Campo agrícola'],
            ['land_category_name' => 'Campo de arroz'],
            ['land_category_name' => 'Campo sin cultivar'],
            ['land_category_name' => 'Canal de aguas residuales'],
            ['land_category_name' => 'Cementerio'],
            ['land_category_name' => 'Depósito de agua para uso no agrícola'],
            ['land_category_name' => 'Estanque'],
            ['land_category_name' => 'Forestal'],
            ['land_category_name' => 'Fraternidad'],
            ['land_category_name' => 'Otros'],
            ['land_category_name' => 'Parque'],
            ['land_category_name' => 'Salar'],
            ['land_category_name' => 'Terreno para canal de agua'],
            ['land_category_name' => 'Terreno para edificios educativos'],
            ['land_category_name' => 'Terreno para tratamiento de aguas'],
            ['land_category_name' => 'Terreno para vivienda'],
            ['land_category_name' => 'Terreno, ferrocarriles, estaciones e inst. rela'],
        ];

        foreach ($landCategories as $category) {
            DB::table('land_category')->insert($category);
        }
    }
}
