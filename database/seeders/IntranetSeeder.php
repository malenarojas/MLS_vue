<?php

namespace Database\Seeders;

use App\Models\{Module, Page, Section, Element, AccessRule};
use Illuminate\Database\Seeder;

class IntranetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module = Module::updateOrCreate(
            ['name' => 'Clases Grabadas'],
            ['image' => '/intranet/Universidad.jpg']
        );

        $page = Page::updateOrCreate(
            ['title' => 'Legal', 'module_id' => $module->id],
            ['image' => '/intranet/Universidad.jpg']
        );

        $module->update(['default_page_id' => $page->id]);

        $section = Section::updateOrCreate(
            ['title' => 'Semana 1', 'page_id' => $page->id],
            ['order' => 1]
        );

        $elements = [
            [
                'type' => 'text',
                'content' => ['text' => 'Bienvenidos a la clase de introducción.'],
                'order' => 1,
            ],
            [
                'type' => 'link',
                'content' => [
                    'name' =>
                    'Clase Vue',
                    'url' => 'video-test.mp4'
                ],
                'order' => 2,
            ]
        ];

        foreach ($elements as $el) {
            $element = Element::updateOrCreate(
                ['section_id' => $section->id, 'type' => $el['type'], 'order' => $el['order']],
                ['content' => $el['content']]
            );

            // Asignar reglas solo al elemento tipo link
            if ($el['type'] === 'link') {
                foreach (['Administrador', 'Broker'] as $role) {
                    AccessRule::updateOrCreate([
                        'resource_type' => Module::class,
                        'resource_id' => $module->id,
                        'type' => 'role',
                        'value' => $role,
                        'access_type' => 'view',
                    ]);

                    AccessRule::updateOrCreate([
                        'resource_type' => Module::class,
                        'resource_id' => $module->id,
                        'type' => 'role',
                        'value' => $role,
                        'access_type' => 'download',
                    ]);
                }
            }
        }

        // Permitir acceso al módulo por roles admin y broker
        foreach (['Administrador', 'Broker'] as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $module->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }



        Module::updateOrCreate(
            ['name' => 'Sin contenido aún'],
            ['image' => null]
        );

        // Módulo con múltiples secciones
        $module2 = Module::updateOrCreate(
            ['name' => 'Módulo con varias secciones'],
            ['image' => '/intranet/Legal.jpg']
        );

        $page2 = Page::updateOrCreate(
            ['title' => 'Página principal', 'module_id' => $module2->id],
            ['image' => null]
        );

        $module2->update(['default_page_id' => $page2->id]);

        foreach (range(1, 3) as $i) {
            $section = Section::updateOrCreate(
                ['page_id' => $page2->id, 'title' => "Sección {$i}"],
                ['order' => $i]
            );

            Element::updateOrCreate(
                ['section_id' => $section->id, 'type' => 'text', 'order' => 1],
                ['content' => ['text' => "Contenido de la sección {$i}"]]
            );
        }
    }
}
