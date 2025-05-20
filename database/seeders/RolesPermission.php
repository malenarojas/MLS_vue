<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'list roles','description' => 'list roles','group' => 'rol'],
            ['name' => 'create roles','description' => 'create roles','group' => 'rol'],
            ['name' => 'edit roles','description' => 'edit roles','group' => 'rol'],
            ['name' => 'edit permissions','description' => 'edit permissions','group' => 'rol'],
        ];

        foreach ($permissions as $permission) {
            $newPermission = Permission::create($permission);
            $sadm = Role::where('name', 'Super Administrador')->first();
            $adm = Role::where('name', 'Administrador')->first();

            $sadm->givePermissionTo($newPermission);
            $adm->givePermissionTo($newPermission);
        }
    }
}
