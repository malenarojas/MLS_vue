<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{

    private $permissions;

    public function __construct()
    {
        /*
        set the default permissions
        */
        $this->permissions =  [
            //Users
            ['name' => 'list users', 'description' => 'list users', 'group' => 'user'],
            ['name' => 'create user', 'description' => 'create user', 'group' => 'user'],
            ['name' => 'edit user', 'description' => 'edit user', 'group' => 'user'],
            ['name' => 'delete user', 'description' => 'delete user', 'group' => 'user'],

            //agents
            ['name' => 'list agents', 'description' => 'list agents', 'group' => 'agent'],
            ['name' => 'create agent', 'description' => 'create agent', 'group' => 'agent'],
            ['name' => 'edit agent', 'description' => 'edit agent', 'group' => 'agent'],
            ['name' => 'delete agent', 'description' => 'delete agent', 'group' => 'agent'],

            //Properties
            ['name' => 'listing.index', 'description' => 'list listing', 'group' => 'listing'],
            ['name' => 'listing.create', 'description' => 'create listing', 'group' => 'listing'],
            ['name' => 'listing.edit', 'description' => 'edit listing', 'group' => 'listing'],
            ['name' => 'listing.delete', 'description' => 'delete listing', 'group' => 'listing'],

            //transactions
            ['name' => 'list transactions', 'description' => 'list transactions', 'group' => 'transaction'],
            ['name' => 'create transaction', 'description' => 'create transaction', 'group' => 'transaction'],
            ['name' => 'edit transaction', 'description' => 'edit transaction', 'group' => 'transaction'],
            ['name' => 'delete transaction', 'description' => 'delete transaction', 'group' => 'transaction'],

            //acm
            ['name' => 'list acm', 'description' => 'list acm', 'group' => 'acm'],

            //Roles y permisos
            ['name' => 'show roles', 'description' => 'show roles', 'group' => 'rol'],

            //Paneles (Deprecated)
            ['name' => 'agent panel', 'description' => 'agent panel', 'group' => 'panels'],
            ['name' => 'office panel', 'description' => 'office panel', 'group' => 'panels'],
            ['name' => 'region panel', 'description' => 'region panel', 'group' => 'panels'],

            // Contactos
            ['name' => 'list contacts', 'description' => 'list contacts', 'group' => 'contact'],
            ['name' => 'office list contact', 'description' => 'delete contact', 'group' => 'contact'],
            ['name' => 'create contact', 'description' => 'create contact', 'group' => 'contact'],
        ];
    }

    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // create permissions
        foreach ($this->permissions as $permission) {
            Permission::create($permission);
        }

        // agregando todos los permiso Super Administrador
        $sadmin = Role::where('name', 'Super Administrador')->first();
        $adm = Role::where('name', 'Administrador')->first();
        $age = Role::where('name', 'Agente')->first();
        $adm->givePermissionTo($this->permissions[0]['name']);
        $adm->givePermissionTo($this->permissions[8]['name']);
        $age->givePermissionTo($this->permissions[8]['name']);

        foreach ($this->permissions as $value) {
            $sadmin->givePermissionTo($value['name']);
            $adm->givePermissionTo($value['name']);
        }
    }
}
