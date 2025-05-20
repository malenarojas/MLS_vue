<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};


class ReunionesEventosProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el módulo Reuniones y Eventos
        $module = Module::updateOrCreate([
            'name' => 'Reuniones y Eventos RE/MAX'
        ], [
            'image' => '/intranet/reuniones-eventos.jpg'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Reuniones y Eventos',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/reuniones-eventos.jpg',
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (solo Administradores y Agentes)
        $rolesPermitidos = ['Administrador', 'Agente', 'Broker'];
        foreach ($rolesPermitidos as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $module->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }

        // Definir las secciones y elementos
        $secciones = [
            'Detalles de Eventos' => [
                [
                    'type' => 'image',
                    'name' => 'Detalles de Eventos',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/05_DETALLES.jpg',
                    'description' => 'Información detallada sobre los eventos programados'
                ]
            ],
            'Calendario Enero 2021' => [
                [
                    'type' => 'image',
                    'name' => 'Calendario Enero 2021',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/01_ENERO_2021.jpg',
                    'description' => 'Eventos programados para Enero 2021'
                ]
            ],
            'Calendario Febrero 2021' => [
                [
                    'type' => 'image',
                    'name' => 'Calendario Febrero 2021',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/02_FEBRERO_2021.jpg',
                    'description' => 'Eventos programados para Febrero 2021'
                ]
            ],
            'Calendario Marzo 2021' => [
                [
                    'type' => 'image',
                    'name' => 'Calendario Marzo 2021',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/03_MARZO_2021.jpg',
                    'description' => 'Eventos programados para Marzo 2021'
                ]
            ],
            'Calendario Abril 2021' => [
                [
                    'type' => 'image',
                    'name' => 'Calendario Abril 2021',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/04_ABRIL_2021.jpg',
                    'description' => 'Eventos programados para Abril 2021'
                ]
            ]
        ];

        // Crear secciones y elementos
        $order = 1;
        foreach ($secciones as $title => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $page->id,
                'title' => $title,
            ], [
                'order' => $order++,
            ]);

            $elementOrder = 1;
            foreach ($elementos as $elemento) {
                $content = [
                    'name' => $elemento['name'],
                    'url' => $elemento['url']
                ];

                if (isset($elemento['description'])) {
                    $content['description'] = $elemento['description'];
                }

                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $elemento['type'],
                    'order' => $elementOrder++,
                ], [
                    'content' => $content
                ]);
            }
        }
    }
}
