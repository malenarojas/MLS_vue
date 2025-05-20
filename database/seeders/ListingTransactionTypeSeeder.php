<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListingTransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $listingTransactionType = [
            [
                'name' => 'Venta',
            ],
            [
                'name' => 'Alquiler',
            ],
            [
                'name' => 'Anticrético',
            ],
        ];

        foreach ($listingTransactionType as $attributes) {
            DB::table('listing_transaction_types')->insert($attributes);
        }
    }
}
