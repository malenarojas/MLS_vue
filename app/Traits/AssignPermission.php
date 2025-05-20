<?php

namespace App\Traits;

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait AssignPermission
{
  const GUARD_NAME = 'web';

  public function assignPermissionToRole(array $permissions)
  {
    foreach ($permissions as $permissionData) {
      // Crear o actualizar el permiso
      $permission = Permission::updateOrCreate(
        ['name' => $permissionData['name'], 'guard_name' => self::GUARD_NAME],
        [
          'description' => $permissionData['description'],
          'group' => $permissionData['group']
        ]
      );

      if (!isset($permissionData['roles'])) {
        continue;
      }

      // Buscar los roles que deben tener este permiso
      $roles = Role::whereIn('name', $permissionData['roles'])->get();

      // Asignar el permiso a cada rol sin eliminar otros permisos
      foreach ($roles as $role) {
        $role->givePermissionTo($permission);
      }
    }
  }

  public function assignPermissionToUser(array $permission)
  {
    // Asignar los permisos de los roles directamente a los usuarios que tienen esos roles
    $users = User::whereHas('roles')->get(); // Obtener todos los usuarios que tienen roles
    foreach ($users as $user) {
      // Obtener todos los permisos de los roles del usuario
      $permissions = $user->roles->flatMap(fn($role) => $role->permissions)->unique();

      // Asignar esos permisos al usuario de forma directa
      $user->syncPermissions($permissions);
    }
  }
}
