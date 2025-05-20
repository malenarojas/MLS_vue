<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [

            [
                'name' => 'Dashboard',
                'icon' => 'pi pi-objects-column',
                'route' => 'dashboard',
                'permission_id' => 1,
            ],
            /*[
                'name' => 'Usuarios',
                'icon' => 'pi pi-user',
                'route' => '/users',
                'permission_id' => 1,
                'children' => [
                    [
                        'name' => 'Nuevo',
                        'route' => '/users/create',
                        'permission_id' => null,
                    ],
                    [
                        'name' => 'Miembro',
                        'route' => '/users/member',
                        'permission_id' => null,
                    ],
                    [
                        'name' => 'Grupo',
                        'route' => '/users/group',
                        'permission_id' => null,
                    ],
                    [
                        'name' => 'Buscar',
                        'route' => '/users/search',
                        'permission_id' => null,
                    ],
                ],
            ],*/
            [
                'name' => 'Propiedades',
                'icon' => 'pi pi-home',
                'route' => 'listings.index',
                'permission_name' => 'listing.index',
                /*'children' => [
                    [
                        'name' => 'Crear',
                        'route' => '/listings/create',
                        'permission_id' => 9,
                    ],
                ],*/
            ],
            [
                'name' => 'Control de calidad',
                'icon' => 'pi pi-check',
                'route' => 'qualitycontrol.index',
                'permission_name' => 'quality_control.index'
            ],
            /*
            [
                'name' => 'Transacciones',
                'icon' => 'pi pi-money-bill',
                'route' => '/transactions',
                'permission_id' => 13,
            ],
            [
                'name' => 'ACM',
                'icon' => 'pi pi-chart-bar',
                'route' => '/marketanalysis',
                'permission_id' => 17,
            ],
            [
                'name' => 'Agents',
                'icon' => ' pi pi-users',
                'route' => '/agents',
                'permission_id' => 5,
                'children' => [
                    [
                        'name' => 'Crear',
                        'route' => '/agents/create',
                        'permission_id' => 6,

                    ]
                ],
            ],
            */
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

        // Si tiene hijos, recorre y crea cada uno de ellos
        if (isset($menuItem['children']) && is_array($menuItem['children'])) {
            foreach ($menuItem['children'] as $child) {
                $this->createMenuItem($child, $menu->id);
            }
        }
    }
}
