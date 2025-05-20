<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('social_networks')->insert([
            [
                'name' => 'Facebook',
                'state' => 1, // Estado habilitado
                'url' => 'https://facebook.com/agent_profile',
                'agent_id' => 1, // Agregar siempre el agent_id
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Twitter',
                'state' => 1,
                'url' => 'https://twitter.com/agent_profile',
                'agent_id' => 1, // Agregar agent_id aquí
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'LinkedIn',
                'state' => 1,
                'url' => 'https://linkedin.com/in/agent_profile',
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Instagram',
                'state' => 1,
                'url' => 'https://instagram.com/agent_profile',
                'agent_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        $this->command->info('Social Networks seeded successfully.');

    }
}
