<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TransactionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //name, group, descriptions

        $permissions = [
            [
                'name' => 'show transaction',
                'description' => 'Show transaction',
                'group' => 'transaction'
            ],
            [
                'name' => 'change status',
                'description' => 'Change status',
                'group' => 'transactions'
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
