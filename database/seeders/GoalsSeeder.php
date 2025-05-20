<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class GoalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = Permission::create([
            'name' => 'create goals',
            'guard_name' => 'web',
            'group' => 'goals',
            'description' => 'Create and update goals'
        ]);

        $admins = User::role('Administrador')->get();
        foreach($admins as $admins) {
            $admins->givePermissionTo($permission);
        }

        $superAdmins = User::role('Super Administrador')->get();
        foreach($superAdmins as $superAdmin) {
            $superAdmin->givePermissionTo($permission);
        }

        $supports = User::role('Soporte')->get();
        foreach($supports as $support) {
            $support->givePermissionTo($permission);
        }

        $brokers = User::role('Broker')->get();
        foreach($brokers as $broker) {
            $broker->givePermissionTo($permission);
        }

        $menuItem = MenuItem::create([
            'name' => 'Metas',
            'route' => 'goals.index',
            'icon' => 'pi pi-chart-line',
            'permission_id' => $permission->id,
        ]);
    }
}
