<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = [
            'name' => 'show reports',
            'description' => 'show reports',
            'group' => 'reports'
        ];

        $sadmin = Role::where('name', 'Super Administrador')->first();
        $adm = Role::where('name', 'Administrador')->first();

        $newPermission = Permission::create($permission);
        $sadmin->givePermissionTo($permission['name']);
        $adm->givePermissionTo($permission['name']);

        $menuItem = [
            'name' => 'Reportes',
            'route' => '/reports',
            'icon' => 'pi pi-file-check',
            'permission_id' => $newPermission->id
        ];

        MenuItem::create($menuItem);
    }
}
