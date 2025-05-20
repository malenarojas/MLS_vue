<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LandUseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landUses = [
            ['land_use_name' => 'No seleccionado'],
            ['land_use_name' => 'Area Comercial'],
            ['land_use_name' => 'Área comercial del vecindario'],
            ['land_use_name' => 'Area Industrial'],
            ['land_use_name' => 'Area Industrial exclusiva'],
            ['land_use_name' => 'Area Semi-Industrial'],
            ['land_use_name' => 'Area Semi-Residencial (Mixto)'],
            ['land_use_name' => 'Cat.I Area Residencial'],
            ['land_use_name' => 'Cat.I Excl. Área Alto y Med Densidad Constr. Res.'],
            ['land_use_name' => 'Cat.I Excl. Área de Bajo Densidad Constr. Res.'],
            ['land_use_name' => 'Cat.II Area Residencial'],
            ['land_use_name' => 'Cat.II Excl. Área Alto y Med Densidad Constr. Res.'],
            ['land_use_name' => 'Cat.II Excl. Área de Bajo Densidad Constr. Res.'],
            ['land_use_name' => 'No especificado'],
        ];

        foreach ($landUses as $landUse) {
            DB::table('land_uses')->insert($landUse);
        }
    }
}
