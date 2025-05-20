<?php

namespace Database\Seeders;

use App\Models\RemaxTitle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RemaxTitleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{

		$titulos = [
			['Agente Asociado', '1'],
			['Asistente con Licencia', '1'],
			['Gerente', '1'],
			['Propietario', '1'],
			['Administration Staff', '1'],
			['CMN Administrator', '1'],
			['Customer Care Manager', '1'],
			['Director of First Impression', '1'],
			['Financial Advisor', '1'],
			['Investing Partner', '1'],
			['Lettings Advisor', '1'],
			['Marketing Manager', '1'],
			['Mortgage Advisor', '1'],
			['Operating Partner', '1'],
			['Rental Manager', '1'],
			['Team Leader', '1'],
			['Team Manager', '1'],

			['Broker Manager','2'],
			['Gerente de Oficina','2'],
			['Personal Administrativo','2'],
		];

		foreach ($titulos as $titulo) {
			RemaxTitle::create([
				'name' => $titulo[0],
				'user_type_id' => $titulo[1],
			]);
		}
	}
}
