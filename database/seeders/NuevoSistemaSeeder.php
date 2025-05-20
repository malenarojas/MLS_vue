<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};

class NuevoSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el módulo Nuevo Sistema
        $moduleNuevoSistema = Module::updateOrCreate([
            'name' => 'Nuevo Sistema'
        ], [
            'image' => '/intranet/nuevo-sistema.jpg' // Asegúrate de tener esta imagen o cambia la ruta
        ]);

        // Página principal del módulo
        $pageNuevoSistema = Page::updateOrCreate([
            'title' => 'Nuevo Sistema',
            'module_id' => $moduleNuevoSistema->id,
        ], [
            'image' => '/intranet/nuevo-sistema.jpg',
        ]);
        $moduleNuevoSistema->update(['default_page_id' => $pageNuevoSistema->id]);

        // Roles permitidos para acceder al módulo
        $rolesPermitidos = ['Administrador', 'Broker', 'Agente']; // Ajusta los roles según necesites

        foreach ($rolesPermitidos as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $moduleNuevoSistema->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }

        // Páginas hijas (subpáginas)
        $pagecreacionlistings = Page::updateOrCreate([
            'title' => 'Creacion de Captacion',
            'module_id' => $moduleNuevoSistema->id,
            'parent_id' => $pageNuevoSistema->id,
        ], [
            'image' => '/intranet/manuales.jpg',
        ]);



        // Sección para Manuales de Usuario
        $sectioncreacionlistings = Section::updateOrCreate([
            'page_id' => $pagecreacionlistings->id,
            'title' => 'Creacion de Propiedades',
        ], [
            'order' => 1,
        ]);

        $manuales = [
            ['name' => 'Listing Principal', 'url' => 'https://drive.google.com/file/d/1SbL1ikWofgkT-yRiDsM47quPIcpWUiKX/view?usp=drive_link'],
            ['name' => 'Listing Final', 'url' => 'https://drive.google.com/file/d/1UXfNgJvCvXX8gH3oqPhxvtZmQsVb1Xc2/view?usp=drive_link'],
            ['name' => 'Listing Descripciones', 'url' => 'https://drive.google.com/file/d/1qrqVdbvidQMGZtNpONwkwsBOZKly40PT/view?usp=drive_link'],
        ];

        $order = 1;
        foreach ($manuales as $item) {
            Element::updateOrCreate([
                'section_id' => $sectioncreacionlistings->id,
                'type' => 'link',
                'order' => $order++,
                'content' => [
                    'name' => $item['name'],
                    'url' => $item['url'],
                ],
            ]);
        }


        // Secciones para la página principal del módulo
        $seccionesNuevoSistema = [
            'Creacion de Propiedades' => [
                ['type' => 'page', 'name' => 'Creacion de Propiedades', 'page_id' => $pagecreacionlistings->id],
            ],
        ];

        $order = 1;
        foreach ($seccionesNuevoSistema as $titulo => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $pageNuevoSistema->id,
                'title' => $titulo,
            ], [
                'order' => $order++,
            ]);

            $elementOrder = 1;
            foreach ($elementos as $item) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $item['type'],
                    'order' => $elementOrder++,
                    'content' => $item['type'] === 'page'
                        ? [
                            'name' => $item['name'],
                            'page_id' => $item['page_id'],
                        ]
                        : [
                            'name' => $item['name'],
                            'url' => $item['url'],
                        ],
                ]);
            }
        }
    }
}
