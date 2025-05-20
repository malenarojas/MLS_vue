<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name_market_status' => 'No seleccionado'],
            ['name_market_status' => 'Guaranteed Returns'],
            ['name_market_status' => 'Great Value'],
            ['name_market_status' => 'MarketStatus1'],
            ['name_market_status' => 'MarketStatus2'],
            ['name_market_status' => 'MarketStatus3'],
            ['name_market_status' => 'Necesita renovación'],
            ['name_market_status' => 'Nueva Captación en el Mercado'],
            ['name_market_status' => 'Fuera de plano'],
            ['name_market_status' => 'Precio reducido'],
            ['name_market_status' => 'Rebajado'],
            ['name_market_status' => 'Renovado'],
            ['name_market_status' => 'En Construcción / En Planos'],
            ['name_market_status' => 'Por Estrenar'],
            ['name_market_status' => 'Reconstruida/ Renovada'],
            ['name_market_status' => 'Usada'],
            ['name_market_status' => 'Remate/Ejecución de hipoteca'],
            ['name_market_status' => 'En subasta'],
            ['name_market_status' => 'Mejor Oferta'],
            ['name_market_status' => 'BLACK FRIDAY']
        ];

        foreach ($statuses as $status) {
            DB::table('market_status')->insert($status);
        }
    }
}
