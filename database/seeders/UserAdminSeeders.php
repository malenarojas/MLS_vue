<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class UserAdminSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuarios Administradores
        $adminUsers = [
            [
                'name_to_show' => 'María José Ibañez',
                'username' => 'MariaIbanez',
                'email' => 'mariaibanez@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Agustín Acebo Pedraza',
                'username' => 'AgustinAcebo',
                'email' => 'agustinacebo@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'María Claudia Flores',
                'username' => 'MariaFlores',
                'email' => 'mariaflores@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Oliver Viera',
                'username' => 'OliverViera',
                'email' => 'oliverviera@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Marianne Wende Hetz',
                'username' => 'MarianneWende',
                'email' => 'mariannewende@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'María del Pilar Herrera Sánchez',
                'username' => 'PilarHerrera',
                'email' => 'pilarherrera@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Evelin Corrales',
                'username' => 'EvelinCorrales',
                'email' => 'evelincorrales@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Fabricio José Luzio Cahill',
                'username' => 'FabricioLucio',
                'email' => 'fabriciolucio@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Sandra Peña Pérez',
                'username' => 'SandraPena',
                'email' => 'sandrapena@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Julisa Téllez López',
                'username' => 'JulisaTellez',
                'email' => 'julisatellez@example.com',
                'password' => '123456789'
            ],
            [
                'name_to_show' => 'Iván Carpio',
                'username' => 'IvanCarpio',
                'email' => 'ivancarpio@example.com',
                'password' => '123456789'
            ]
        ];

        foreach ($adminUsers as $userData) {
            $user = User::create([
                'name_to_show' => $userData['name_to_show'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password'])
            ]);

            $user->assignRole('Administrador');
        }

        // Usuarios adicionales del sistema
        $systemUsers = [
            [
                'name_to_show' => 'Super Administrador',
                'username' => 'sadmin',
                'email' => 'sadmin@admin.com',
                'password' => 'Adm1N202F@',
                'role' => 'Super Administrador'
            ],
            [
                'name_to_show' => 'Administrador Principal',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'password' => '123456789',
                'role' => 'Administrador'
            ],
            [
                'name_to_show' => 'Soporte Regional',
                'username' => 'regional',
                'email' => 'regional@regional.com',
                'password' => '123456789',
                'role' => 'Soporte'
            ]
        ];

        foreach ($systemUsers as $userData) {
            $user = User::create([
                'name_to_show' => $userData['name_to_show'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password'])
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
