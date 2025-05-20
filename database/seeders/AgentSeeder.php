<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

      // Crear el usuario manualmente
      $userId = DB::table('users')->insertGetId([
        'first_name' => 'agente',
        'username' => 'agente',
        'email' => 'agente@admin.com',
        'password' => Hash::make('123456789'),
        'user_type_id' => DB::table('user_types')->where('name', 'Agent')->value('id'), // Asignar user_type_id
        'remax_title_id' => DB::table('remax_titles')->where('name', 'Broker')->value('id'), // Relación con remax_title
        'remax_title_to_show_id' => DB::table('remax_title_to_shows')->where('name', 'Team Leader')->value('id'), // Relación con remax_title_to_show
        'team_status_id' => DB::table('team_statuses')->where('name', 'Active')->value('id'), // Relación con team_status
        'customer_preference_id' => DB::table('customer_preferences')->where('name', 'Compradores')->value('id'), // Relación con customer_preference
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Asignar rol al usuario (si usas spatie/laravel-permission u otro paquete similar)
    DB::table('model_has_roles')->insert([
        'role_id' => 4, // Asegúrate de que este ID coincide con el rol "Agente" en tu tabla roles
        'model_type' => 'App\Models\User',
        'model_id' => $userId,
    ]);

    // Crear el agente manualmente
    $agents = [
        [
            'agent_internal_id' => 'AG12345',
            'office_id' => 1,
            'region_id' => 120,
            'user_id' => $userId, // Asociar al usuario recién creado
            'studies' => 'Licenciatura en Marketing',
            'additional_education' => 'Maestría en Administración de Empresas',
            'image_name' => 'https://example.com/images/agent12345.jpg',
            'previous_occupation' => 'Consultor de Ventas',
            'license_type' => 'Agente',
            'license_department' => 'Santa Cruz',
            'year_obtained_license' => '2018',
            'expiration_date_license' => '2026-12-31',
            'license_number' => 'LIC-67890',
            'marketing_slogan' => 'La mejor opción en bienes raíces',
            'website_descripction' => 'Especialista en ventas residenciales y comerciales.',
            'countries_interested' => 'Bolivia, Argentina, Brasil',
            'meta_tag_description' => 'Agente experto en bienes raíces con más de 10 años de experiencia.',
            'bullet_point_one' => 'Atención personalizada a cada cliente.',
            'bullet_point_two' => 'Amplio conocimiento del mercado inmobiliario.',
            'bullet_point_three' => 'Certificación internacional en ventas.',
            'meta_tag_keywords' => 'agente, bienes raíces, ventas, marketing',
            'commission_percentage' => 12.5,

            'created_at' => now(),
            'updated_at' => now(),
        ],
        // Puedes añadir más agentes aquí si lo necesitas
    ];

    // Inserta los datos en la tabla `agents`
    DB::table('agents')->insert($agents);

    // Mensaje de confirmación
    $this->command->info('Agente y usuario creados exitosamente.');
}
}
