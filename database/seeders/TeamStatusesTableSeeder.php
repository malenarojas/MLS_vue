<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Lista de team_statuses
         $teamStatuses = [
            ['name' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inactive', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pending Approval', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Approved', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Archived', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deleted', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insertar los datos en la tabla team_statuses
        DB::table('team_statuses')->insert($teamStatuses);
    }
}
