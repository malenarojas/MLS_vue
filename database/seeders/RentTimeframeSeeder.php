<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RentTimeframeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timeframes = [
            'Mensual',
            'Trimestral',
            'Anual',
        ];

        foreach ($timeframes as $timeframe) {
            \App\Models\RentTimeframe::updateOrCreate([
                'name' => $timeframe,
            ]);
        }
    }
}
