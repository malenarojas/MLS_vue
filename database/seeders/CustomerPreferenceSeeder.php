<?php

namespace Database\Seeders;

use App\Models\CustomerPreference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $preferencias = [
            'Compradores',
            'Vendedores',
            'Compradores & Vendedores'
        ];

        foreach ($preferencias as $preferencia) {
            CustomerPreference::create(['name' => $preferencia]);
        }
    }
}
