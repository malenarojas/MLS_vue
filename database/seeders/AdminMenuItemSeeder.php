<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Contracts\Permission;
use Spatie\Permission\Models\Permission as ModelsPermission;

class AdminMenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        $menuItemPermission = ModelsPermission::create(['name' => 'admin_menu']);

        $admins = User::role('Administrador')->get();
        foreach($admins as $admins) {
            $admins->givePermissionTo($menuItemPermission);
        }

        $superAdmins = User::role('Super Administrador')->get();
        foreach($superAdmins as $superAdmin) {
            $superAdmin->givePermissionTo($menuItemPermission);
        }

        $supports = User::role('Soporte')->get();
        foreach($supports as $support) {
            $support->givePermissionTo($menuItemPermission);
        }
        
        MenuItem::create([
            'name' => 'Administracion',
            'route' => 'admin.index',
            'icon' => 'pi pi-sliders-h',
            'permission_id' => $menuItemPermission->id,
            'parent_id' => null,
        ]);

        DB::commit();
    }
}
