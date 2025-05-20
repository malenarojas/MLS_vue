<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserTerminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions =  [
            //Users
            ['name' => 'termination users', 'description' => 'Terminacion de Usuario', 'group' => 'user'],

        ];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'], // clave única
                ['description' => $perm['description'], 'group' => $perm['group']]
            );
        }
    }
}
