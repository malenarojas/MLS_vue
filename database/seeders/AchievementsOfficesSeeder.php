<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementsOfficesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('achievements_offices')->insert([
            [
                'name_achievements' => 'Colección',
                'achievement_description' => 'Company Collection es la mejor',
                'image' => 'collection.png', // Asegúrate que este archivo exista en public/storage/logros o la ruta que uses
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Comercial',
                'achievement_description' => 'Especialización en Propiedades Comerciales',
                'image' => 'commercial.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Succeed',
                'achievement_description' => 'Agent mentoring program that makes you a better agent',
                'image' => 'succeed.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
