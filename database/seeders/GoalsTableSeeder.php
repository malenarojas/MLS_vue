<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GoalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('goals')->insert([
            [
                'commision_goal' => 15000.50,
                'contact_goal' => 50,
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'commision_goal' => 20000.00,
                'contact_goal' => 70,
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'commision_goal' => 18000.75,
                'contact_goal' => 60,
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
