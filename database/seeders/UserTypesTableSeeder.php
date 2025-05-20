<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar datos en la tabla user_types
        $userTypes = [
            ['name' => 'Agent', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office Admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Office Staff', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Region Admin', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('user_types')->insert($userTypes);
    }
}
