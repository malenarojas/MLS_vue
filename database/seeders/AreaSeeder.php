<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\AreaSpeciality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            ['COM', 'Comercial(Esta sección es para Agentes Asociados que trabajan solo con transacciones comerciales)', 'is_base' => true],
            ['RES', 'Residencial(Esta sección es para Agentes Asociados transacciones residenciales)', 'is_base' => true],
            ['COMRES', 'Comercial con Residencial(Esta sección es para Agentes Asociados que trabajan ambas áreas (comercial y residencial), pero en su mayoria comercial.)'],
            ['RESCOM', 'Residencial con Comercial(Esta sección es para Agentes Asociados que trabajan ambas áreas, comercial y residencial pero residencial en su mayoría)']
        ];

        foreach ($areas as $area) {
            $area = Area::create([
                'name' => $area[0],
                'description' => $area[1],
                'is_base' => $area['is_base'] ?? false
            ]);

            AreaSpeciality::create([
                'area_id' => $area->id
            ]);
        }
    }
}
