<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qualifications = [
            [
                'qualification' => 4.5,
                'comment' => 'Excelente desempeño y atención al cliente.',
                'reference_type' => 'Client Feedback',
                'agent_id'=> 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'qualification' => 3.8,
                'comment' => 'Cumplió con las expectativas generales.',
                'reference_type' => 'Peer Review',
                'agent_id'=> 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'qualification' => 4.9,
                'comment' => 'Altamente recomendado por su compromiso.',
                'reference_type' => 'Manager Feedback',
                'agent_id'=> 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'qualification' => 3.2,
                'comment' => 'Áreas de mejora identificadas en comunicación.',
                'reference_type' => 'Self Assessment',
                'agent_id'=> 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('qualifications')->insert($qualifications);

        $this->command->info('Qualifications seeded successfully.');
    }
}
