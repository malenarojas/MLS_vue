<?php

namespace Database\Seeders;

use App\Traits\AssignPermission;
use Illuminate\Database\Seeder;

class ListingPermissionSeeder extends Seeder
{
    use AssignPermission;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'listing.show_status',
                'description' => 'listing show status',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'listing.show_offices',
                'description' => 'listing show offices',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'listing.show_agents',
                'description' => 'listing show agents',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'listing.index',
                'description' => 'list listing',
                'group' => 'listing',
                'roles' => ['Administrador', 'Broker', 'Agente'],
            ],
            [
                'name' => 'listing.create',
                'description' => 'create listing',
                'group' => 'listing',
                'roles' => ['Administrador', 'Broker', 'Agente'],
            ],
            [
                'name' => 'listing.create.select_office',
                'description' => 'listing create select office',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'listing.create.select_agent',
                'description' => 'listing create select agent',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'listing.edit',
                'description' => 'edit listing',
                'group' => 'listing',
                'roles' => ['Administrador', 'Broker', 'Agente'],
            ],
            [
                'name' => 'listing.delete',
                'description' => 'delete listing',
                'group' => 'listing',
                'roles' => ['Administrador'],
            ],
        ];

        $this->assignPermissionToRole($permissions);
        // $this->assignPermissionToUser($permissions);

        // // Buscar o crear el rol Administrador
        // $adminRole = Role::firstOrCreate(['name' => 'Administrador']);

        // // Crear permisos si no existen y asignarlos al rol
        // foreach ($permissions as $permissionData) {
        //     $permission = Permission::firstOrCreate(['name' => $permissionData['name']], $permissionData);
        //     $adminRole->givePermissionTo($permission);
        // }

        // // Asignar el rol "Administrador" al usuario con username 'admin'
        // $adminUser = User::where('username', 'admin')->first();
        // if ($adminUser) {
        //     // Asignar el rol
        //     $adminUser->assignRole($adminRole);

        //     // Obtener los permisos del rol y asignarlos directamente al usuario
        //     $rolePermissions = $adminRole->permissions;
        //     $adminUser->syncPermissions($rolePermissions);
        // }
    }
}
