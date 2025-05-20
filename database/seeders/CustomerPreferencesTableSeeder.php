<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerPreferencesTableSeeder extends Seeder
{
  /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Lista de preferencias de cliente
        $customerPreferences = [
            ['id' => 1314, 'name' => 'Compradores', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1316, 'name' => 'Compradores & Vendedores', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 1315, 'name' => 'Vendedores', 'created_at' => now(), 'updated_at' => now()],
        ];

        // Insertar los datos en la tabla customer_preferences
        DB::table('customer_preferences')->insert($customerPreferences);
    }
}
