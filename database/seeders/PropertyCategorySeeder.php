<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PropertyCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name_properties_categories' => 'No seleccionado'],
            ['name_properties_categories' => 'The RE/MAX Collection'],
            ['name_properties_categories' => 'Commercial Properties'],
            ['name_properties_categories' => 'Off Plan'],
            ['name_properties_categories' => 'Otros'],
            ['name_properties_categories' => 'Propiedad Privada'],
            ['name_properties_categories' => 'RE/MAX Comercial'],
            ['name_properties_categories' => 'Resale'],
            ['name_properties_categories' => 'ResidentialArea'],
            ['name_properties_categories' => 'Retirement Village'],
            ['name_properties_categories' => 'Timeshare/Fractional Ownership'],
            ['name_properties_categories' => 'Urbanización Cerrada'],
            ['name_properties_categories' => 'Venta de Negocio'],
            ['name_properties_categories' => 'WithBuildingPermit'],
            ['name_properties_categories' => 'WithinCityPlan'],
            ['name_properties_categories' => 'Fraternidad/Vacacional'],
            ['name_properties_categories' => 'Anticrético'],
            ['name_properties_categories' => 'Quinta'],
            ['name_properties_categories' => 'Nuevo Proyecto'],
            ['name_properties_categories' => 'Industrial'],
            ['name_properties_categories' => 'Terreno'],
            ['name_properties_categories' => 'Lujo'],
            ['name_properties_categories' => 'BLACK FRIDAY'],
        ];

        foreach ($categories as $category) {
            DB::table('properties_category')->insert($category);
        }
    }
}
