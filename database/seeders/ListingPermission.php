<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ListingPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'show status listing',
                'description' => 'Show status listing',
                'group' => 'listing',
                'group' => 'transaction'
            ],
        ];

        foreach ($permissions as $permission) {
            $newPermission = Permission::create($permission);
            $sadm = Role::where('name', 'Super Administrador')->first();
            $adm = Role::where('name', 'Administrador')->first();

            $sadm->givePermissionTo($newPermission);
            $adm->givePermissionTo($newPermission);
        }
    }
}
