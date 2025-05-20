<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AchievementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $achievements = [
            [
                'name_achievements' => 'Remax Commercial',
                'achievement_description' => 'RE/MAX Comercial',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Remax Collection Agent',
                'achievement_description' => 'Agente de RE/MAX Collection',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'iList Certified',
                'achievement_description' => 'Certificado iList.Net',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Remax Bolivia CIRB Certificación',
                'achievement_description' => 'CIRB Certificación',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Remax Bolivia CIG Certificación',
                'achievement_description' => 'CIG Certificación Brokers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'Remax Bolivia CIRB MAX',
                'achievement_description' => 'Trans_Designations_CIRB_MAX',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name_achievements' => 'RE/MAX Bolivia GREATNES BUFFINI',
                'achievement_description' => '100 días de éxito BUFFINI y COMPANY',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('achievements')->insert($achievements);
    }
}
