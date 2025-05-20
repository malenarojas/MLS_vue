<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AgentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'agent.edit.email',
                'description' => 'Permite editar el correo del agente',
                'group' => 'Agent',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'agent.edit.phone_number',
                'description' => 'Permite editar el número de teléfono del agente',
                'group' => 'Agent',
                'roles' => ['Administrador'],
            ],
            [
                'name' => 'agent.edit.socialNetworks',
                'description' => 'Permite editar las redes sociales del agente',
                'group' => 'Agent',
                'roles' => ['Agente'],
            ],
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['description' => $perm['description'], 'group' => $perm['group']]
            );

            foreach ($perm['roles'] as $roleName) {
                $role = Role::firstOrCreate(['name' => $roleName]);
                $role->givePermissionTo($permission);
            }
        }

        // Asignar el rol al usuario admin si existe
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $admin->assignRole('Administrador');
        }
    }
}
