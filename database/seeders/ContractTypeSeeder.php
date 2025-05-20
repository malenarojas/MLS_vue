<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type_list = [
            'Exclusiva',
            'Exclusividad del cliente',
            'No exclusiva'
        ];

        foreach($type_list as $type){
            ContractType::create([
                'name' => $type
            ]);
        }
    }
}
