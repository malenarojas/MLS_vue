<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = MenuItem::create([
            'name' => 'Contactos',
            'route' => 'contacts.index',
            'icon' => 'pi pi-fw pi-users',
        ]);
    }
}
