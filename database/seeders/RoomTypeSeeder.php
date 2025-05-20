<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = [
            [
                'name' => 'Habitación',
            ],
            [
                'name' => 'Sala',
            ],
            [
                'name' => 'Cocina',
            ],
            [
                'name' => 'Baño',
            ],
            [
                'name' => 'Comedor',
            ],
            [
                'name' => 'Baño americano',
            ],
        ];

        RoomType::insert($roomTypes);
    }
}
