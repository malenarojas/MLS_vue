<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$tipos = [
			'Agente Asociado',
			'No Agente Inmobiliario (Staff de oficina)'
		];
        foreach($tipos as $tipo){
			UserType::create([
				'name' => $tipo
			]);
		}

    }
}
