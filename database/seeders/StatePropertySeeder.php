<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name_state_properties' => 'No seleccionado'],
            ['name_state_properties' => 'Ocupada por Inquilino'],
            ['name_state_properties' => 'Ocupada por Propietario'],
            ['name_state_properties' => 'Vacía'],
            ['name_state_properties' => 'Venta en Planos'],
            ['name_state_properties' => 'Constructed'],
            ['name_state_properties' => 'For Demolition'],
            ['name_state_properties' => 'In Bad Condition'],
            ['name_state_properties' => 'In Good Condition'],
            ['name_state_properties' => 'In Very Good Condition'],
            ['name_state_properties' => 'Planned Project'],
            ['name_state_properties' => 'Tenanted | Occupied on Handover'],
            ['name_state_properties' => 'Under Construction'],
            ['name_state_properties' => 'Vacant with Shell & Core'],
        ];

        foreach ($states as $state) {
            DB::table('state_properties')->insert($state);
        }
    }
}
