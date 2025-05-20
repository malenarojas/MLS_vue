<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Element;
use App\Models\Module;
use App\Models\Page;
use App\Models\Section;

class PermissionIntranetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions =  [
            ['name' => 'index intranet','description' => 'pagina de modulos para el intranet' ,'group' => 'Intranet']
        ];

         foreach ($permissions as $permission) {
            Permission::create($permission);
        }
        $menuItems = [

            [
                'name' => 'Intranet',
                'icon' => 'pi pi-objects-column',
                'route' => 'intranet.index',
                'permission_name' => 'index intranet',
            ],
        ];
        foreach ($menuItems as $menuItem) {
            $this->createMenuItem($menuItem);
        }
    }
    private function createMenuItem(array $menuItem, $parentId = null)
    {
        $permissionId = $menuItem['permission_id'] ?? null;
        if (!isset($permissionId)) {
            // Buscar por name
            $permission = Permission::where('name', $menuItem['permission_name'])->first();
            $permissionId = $permission?->id ?? null;
        }

        $menu = MenuItem::updateOrCreate(
            [
                'route' => $menuItem['route'],
                'permission_id' => $permissionId,
                'parent_id' => $parentId,
            ],
            [
                'name' => $menuItem['name'],
                'parent_id' => $parentId,
                'icon' => $menuItem['icon'] ?? null,
                'route' => $menuItem['route'] ?? null,
                'permission_id' => $permissionId,
            ]
        );

        if (isset($menuItem['children']) && is_array($menuItem['children'])) {
            foreach ($menuItem['children'] as $child) {
                $this->createMenuItem($child, $menu->id);
            }
        }
    }
}
