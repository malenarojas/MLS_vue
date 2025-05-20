<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Insertar estados en la tabla 'states'
          DB::table('states')->insert([
            ['name' => 'Beni'],
            ['name' => 'Chuquisaca'],
            ['name' => 'Cochabamba'],
            ['name' => 'La Paz'],
            ['name' => 'Oruro'],
            ['name' => 'Pando'],
            ['name' => 'Potosí'],
            ['name' => 'Santa Cruz'],
            ['name' => 'Tarija'],
        ]);
    }
}
