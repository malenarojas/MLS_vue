<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class FilterPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'can_filter_by_office',
            'can_filter_by_agents'
        ];

        $sadmin = Role::where('name', 'Super Administrador')->first();
        $adm = Role::where('name', 'Administrador')->first();

        // foreach($permissions as $permission){
        //     $newPermission = Permission::create($permission);
        //     $sadmin->givePermissionTo($permission['name']);
        //     $adm->givePermissionTo($permission['name']);
        // }
    }
}
