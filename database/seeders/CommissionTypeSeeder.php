<?php

namespace Database\Seeders;

use App\Models\CommissionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommissionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type_list = [
            'Comisión',
            'Referidos'
        ];

        foreach ($type_list as $type) {
            CommissionType::create([
                'name' => $type
            ]);
        }
    }
}
