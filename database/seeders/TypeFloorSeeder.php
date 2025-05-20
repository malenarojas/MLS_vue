<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeFloorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $floorTypes = [
            'Sótano',
            'Planta Baja',
            'Mezzanine',
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10+',
            '1 - 20',
            'Último Piso',
            'Ático',
            'Penthouse',
            'Medio Piso',
            'Principal',
            'Maisonette',
            'Casa de Servicio',
            'Piso de Arriba',
            'Apartamento en tres niveles',
            'Todo el edificio',
            'Galería',
            'Apartamento Planta Baja',
            'Desván',
            'Duplex',
            '0',
            'ElevatedFloor',
            'Semibasement'
        ];

        foreach ($floorTypes as $type) {
            DB::table('type_floors')->insert([
                'name' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
