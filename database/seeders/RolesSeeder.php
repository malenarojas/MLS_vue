<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RolesSeeder extends Seeder
{

    private $permissions, $user_permissions;

    public function __construct()
    {
    }

    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create the admin role and set all default permissions
        Role::create(['name' => 'Super Administrador']);
        Role::create(['name' => 'Administrador']);
        Role::create(['name' => 'Soporte']);
        Role::create(['name' => 'Agente']);
        Role::create(['name' => 'Broker']);
    }
}
