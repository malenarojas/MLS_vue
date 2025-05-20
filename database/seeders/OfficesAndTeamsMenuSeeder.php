<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;
use App\Models\MenuItem;
use App\Models\User;
use Spatie\Permission\Models\Role;

class OfficesAndTeamsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // 👮‍♂️ Permisos
         $permissions = [
            ['name' => 'list offices', 'description' => 'listado de oficinas', 'group' => 'offices'],
            ['name' => 'list team management', 'description' => 'listado de equipos', 'group' => 'Teams'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // 📦 Menú
        $menuItems = [
            [
                'name' => 'Oficinas',
                'icon' => 'pi pi-building',
                'route' => 'offices.index',
                'parent_id' => 15,
                'permission_name' => 'list offices',
            ],
            [
                'name' => 'Equipos',
                'icon' => 'pi pi-user-plus',
                'route' => 'teammanagement.index',
                'parent_id' => 15,
                'permission_name' => 'list team management',
            ],
        ];

        foreach ($menuItems as $menuItem) {
            $this->createMenuItem($menuItem);
        }

        // 👤 Asignar permisos al admin
        foreach (['Administrador', 'Broker'] as $roleName) {
            $role = Role::where('name', $roleName)->first();

            if ($role) {
                foreach ($permissions as $permission) {
                    $role->givePermissionTo($permission['name']);
                }
            }
        }

    }

    private function createMenuItem(array $menuItem, $parentId = null): void
    {
        $permissionId = $menuItem['permission_id'] ?? null;

        if (!isset($permissionId)) {
            $permission = Permission::where('name', $menuItem['permission_name'])->first();
            $permissionId = $permission?->id;
        }

        $menu = MenuItem::updateOrCreate(
            [
                'route' => $menuItem['route'],
                'permission_id' => $permissionId,
                'parent_id' => $parentId,
            ],
            [
                'name' => $menuItem['name'],
                'icon' => $menuItem['icon'] ?? null,
                'route' => $menuItem['route'],
                'permission_id' => $permissionId,
                'parent_id' => $parentId,
            ]
        );

        if (isset($menuItem['children']) && is_array($menuItem['children'])) {
            foreach ($menuItem['children'] as $child) {
                $this->createMenuItem($child, $menu->id);
            }
        }
    }

}
