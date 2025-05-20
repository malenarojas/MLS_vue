<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name' => 'Boliviano',
                'symbol' => 'BOB'
            ],
            [
                'name' => 'USD',
                'symbol' => '$'
            ],
            [
                'name' => 'EUR',
                'symbol' => '€'
            ],
        ];

        foreach($currencies as $currency){
            Currency::create([
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
            ]);
        }
    }
}
