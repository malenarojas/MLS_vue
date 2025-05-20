<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};

class CalendarioAgentesProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear o actualizar el módulo Calendario Online para Agentes
         $module = Module::updateOrCreate([
            'name' => 'Calendario Online - Agentes RE/MAX'
        ], [
            'image' => '/intranet/Calendario.jpg',
            'description' => 'Calendario oficial de eventos para agentes RE/MAX Bolivia'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Calendario Online',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/Titulo%20Calendario%20de%20Adtividades.jpeg',
            'description' => 'Calendario interactivo con todos los eventos para agentes'
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (solo Agentes y Administradores)
        $rolesPermitidos = ['Administrador', 'Agente'];
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
            /*'Encabezado' => [
                [
                    'type' => 'image',
                    'name' => 'Título Calendario de Actividades',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/Titulo%20Calendario%20de%20Adtividades.jpeg',
                    'description' => 'Calendario de actividades para agentes',
                    'attributes' => [
                        'style' => 'width: 97%; height: auto;'
                    ]
                ]
            ],*/
            'Calendario Principal' => [
                [
                    'type' => 'iframe',
                    'name' => 'Calendario Google para Agentes',
                    'url' => 'https://calendar.google.com/calendar/embed?height=600&wkst=1&bgcolor=%23A79B8E&ctz=America%2FLa_Paz&src=Y185aWNqZjcwcWh1dGM2c2pjNGduY3EzYmM2MEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&src=ZXMuYm8jaG9saWRheUBncm91cC52LmNhbGVuZGFyLmdvb2dsZS5jb20&color=%23150d59&color=%230B8043&showTitle=0',
                    'description' => 'Calendario interactivo con todos los eventos programados',
                ]
            ],
        ];

        // Crear secciones y elementos
        $order = 1;
        foreach ($secciones as $title => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $page->id,
                'title' => $title,
            ], [
                'order' => $order++,
                'visible' => true
            ]);

            $elementOrder = 1;
            foreach ($elementos as $elemento) {
                $content = [
                    'name' => $elemento['name'],
                    'description' => $elemento['description'] ?? null,
                    'url' => $elemento['url'] ?? null
                ];

                if (isset($elemento['content'])) {
                    $content = array_merge($content, $elemento['content']);
                }

                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $elemento['type'],
                    'order' => $elementOrder++,
                ], [
                    'content' => $content,
                ]);
            }
        }
    }
}
