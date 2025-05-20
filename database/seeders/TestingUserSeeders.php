<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingUserSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name_to_show' => 'Testing Agent 1',
                'first_name' => 'Testing',
                'last_name' => 'Agent 1',
                'username' => 'testing_agent_1',
                'email' => 'test1@gmail.com',
                'password' => bcrypt('123')
            ],
            [
                'name_to_show' => 'Testing Agent 2',
                'first_name' => 'Testing',
                'last_name' => 'Agent 2',
                'username' => 'testing_agent_2',
                'email' => 'test2@gmail.com',
                'password' => bcrypt('123')
            ]
        ];

        foreach ($users as $user) {
            $user = User::create($user);
            Agent::create([
                'office_id' => 120090,
                'region_id' => 120,
                'user_id' => $user->id,
                'agent_status_id' => 1,
                'agent_internal_id' => 120090 . ($this->getOfficeAgentsCount() + 1),
            ]);

            $user->assignRole('Agente');
            $user->syncPermissions(['list acm','create property', 'edit property','list properties']);
            
        }
    }

    public function getOfficeAgentsCount () {
        return Agent::where('office_id', 120090)->count();
    }
}
