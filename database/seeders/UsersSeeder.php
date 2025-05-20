<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Agent;
use Illuminate\Support\Facades\DB;


class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->first_name     = 'Super Administrador';
        $user->username     = 'sadmin';
        $user->email    = 'sadmin@admin.com';
        $user->password = Hash::make('Adm1N202F@');
        $user->save();
        $user->assignRole('Super Administrador');

        $user = new User;
        $user->first_name     = 'admin';
        $user->username     = 'admin';
        $user->email    = 'admin@admin.com';
        $user->password = Hash::make('123456789');
        $user->save();
        $user->assignRole('Administrador');

        // Crear usuario y asignar rol
    $userId = DB::table('users')->insertGetId([
        'username' => 'regional',
        'email' => 'regional@regional.com',
        'password' => Hash::make('123456789'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('model_has_roles')->insert([
        'role_id' => DB::table('roles')->where('name', 'Soporte')->value('id'),
        'model_type' => 'App\Models\User',
        'model_id' => $userId
    ]);

    // Crear usuario Broker y agente asociado
    $userBId = DB::table('users')->insertGetId([
        'first_name' => 'Brocker',
        'username' => 'broker',
        'email' => 'broker@broker.com',
        'password' => Hash::make('123456789'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('model_has_roles')->insert([
        'role_id' => DB::table('roles')->where('name', 'Broker')->value('id'),
        'model_type' => 'App\Models\User',
        'model_id' => $userBId
    ]);
    DB::table('agents')->insert([
        'user_id' => $userBId,
        'region_id' => 120,
        'office_id' => 120090,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Crear usuario Broker2 y agente asociado
    $userBBId = DB::table('users')->insertGetId([
        'first_name' => 'Brocker2',
        'username' => 'broker2',
        'email' => 'broker2@broker.com',
        'password' => Hash::make('123456789'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    DB::table('model_has_roles')->insert([
        'role_id' => DB::table('roles')->where('name', 'Broker')->value('id'),
        'model_type' => 'App\Models\User',
        'model_id' => $userBBId
    ]);
    DB::table('agents')->insert([
        'user_id' => $userBBId,
        'region_id' => 120,
        'office_id' => 120090,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    }
}
