<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MultimediaTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $multimediaTypes = [
            // ['name' => '360º'],
            // ['name' => 'Panorámica'],
            ['name' => 'Normal'],
        ];

        foreach ($multimediaTypes as $multimediaType) {
            \App\Models\MultimediaType::create($multimediaType);
        }
    }
}