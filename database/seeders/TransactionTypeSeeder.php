<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type_list = [
            'Listing',
            'Selling'
        ];

        foreach($type_list as $type){
            TransactionType::create([
                'name' => $type
            ]);
        }
    }
}
