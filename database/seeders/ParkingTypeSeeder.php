<?php

namespace Database\Seeders;

use App\Models\ParkingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Estacionamiento en garaje',
            'Estacionamiento en exteriores',
            'Estacionamiento subterráneo'
        ];

        foreach ($tipos as $tipo) {
            DB::table('parking_types')->insert([
                'name_parking_type' => $tipo
            ]);
        }
    }
}
