<?php

namespace Database\Seeders;

use App\Traits\AssignPermission;
use Illuminate\Database\Seeder;

class ListingQualityControlSeeder extends Seeder
{
    use AssignPermission;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'quality_control.index',
                'description' => 'list quality control',
                'group' => 'quality_control',
                'roles' => ['Administrador', 'Broker'],
            ],
            [
                'name' => 'quality_control.store',
                'description' => 'Aceptar o rechazar listing',
                'group' => 'quality_control',
                'guard_name' => 'web',
                'roles' => ['Broker'],
            ],
        ];

        $this->assignPermissionToRole($permissions);
        $this->assignPermissionToUser($permissions);

        // foreach ($permissions as $permissionData) {
        //     // Crear o actualizar el permiso
        //     $permission = Permission::updateOrCreate(
        //         ['name' => $permissionData['name'], 'guard_name' => $permissionData['guard_name']],
        //         [
        //             'description' => $permissionData['description'],
        //             'group' => $permissionData['group']
        //         ]
        //     );

        //     // Buscar los roles que deben tener este permiso
        //     $roles = Role::whereIn('name', $permissionData['roles'])->get();

        //     // Asignar el permiso a cada rol sin eliminar otros permisos
        //     foreach ($roles as $role) {
        //         $role->givePermissionTo($permission);
        //     }
        // }

        // // Asignar los permisos de los roles directamente a los usuarios que tienen esos roles
        // $users = User::whereHas('roles')->get(); // Obtener todos los usuarios que tienen roles
        // foreach ($users as $user) {
        //     // Obtener todos los permisos de los roles del usuario
        //     $permissions = $user->roles->flatMap(fn($role) => $role->permissions)->unique();

        //     // Asignar esos permisos al usuario de forma directa
        //     $user->syncPermissions($permissions);
        // }
    }
}
