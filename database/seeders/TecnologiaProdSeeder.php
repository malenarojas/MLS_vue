<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};


class TecnologiaProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Crear o actualizar el módulo Tecnología
       $module = Module::updateOrCreate([
        'name' => 'Tecnología RE/MAX'
    ], [
        'image' => '/intranet/tecnologia.jpg'
    ]);

    // Crear página principal
    $page = Page::updateOrCreate([
        'title' => 'Tecnología RE/MAX',
        'module_id' => $module->id,
    ], [
        'image' => '/intranet/tecnologia.jpg',
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
        'Photofy' => [
            [
                'type' => 'image',
                'name' => 'Logo Photofy',
                'url' => '/Sites/remaxbolivia/intranet/tecnologia/logoPhotofy.png',
                'description' => 'Para generar publicaciones como un profesional'
            ],
            [
                'type' => 'link',
                'name' => 'Acceso a Photofy',
                'url' => 'https://resources.remax.net/products/photofy'
            ],
            [
                'type' => 'link',
                'name' => 'Cómo Instalar Photofy - Tutorial paso a paso',
                'url' => '/Sites/remaxbolivia/intranet/tecnologia/C%C3%B3mo%20Instalar%20Photofy%20para%20Agentes%20RE_MAX.%20Tutorial%20paso%20a%20paso.mp4'
            ]
        ],
        'MAXCenter' => [
            [
                'type' => 'image',
                'name' => 'MAXCenter',
                'url' => '/Sites/remaxbolivia/intranet/tecnologia/maxcenter.jpg',
                'description' => 'Plataforma para descargar recursos, compartir mejores prácticas y generar contenido de marketing'
            ],
            [
                'type' => 'link',
                'name' => 'Acceso a MAXCenter',
                'url' => 'https://www.remax.net/'
            ],
            [
                'type' => 'link',
                'name' => 'Registro a Maxcenter',
                'url' => '/Sites/remaxbolivia/intranet/Documents/01%20Registro%20a%20Maxcenter.ppsx'
            ],
            [
                'type' => 'link',
                'name' => 'Recuperación de contraseña MAXCenter',
                'url' => '/Sites/remaxbolivia/intranet/Documents/02%20Recuperacion%20de%20contrase%C3%B1a%20MAXCenter.ppsx'
            ]
        ],
        'RE/MAX Hustle' => [
            [
                'type' => 'image',
                'name' => 'RE/MAX Hustle',
                'url' => '/Sites/remaxbolivia/intranet/tecnologia/remaxHustle.jpg',
                'description' => 'Soluciones de marketing llave en mano producidas profesionalmente'
            ],
            [
                'type' => 'link',
                'name' => 'Acceso a RE/MAX Hustle',
                'url' => 'https://www.remaxhustle.com/'
            ]
        ],
        'Acortador de URLs' => [
            [
                'type' => 'image',
                'name' => 'Acortador de URLs',
                'url' => '/Sites/remaxbolivia/intranet/tecnologia/urlSortener.jpg',
                'description' => 'Acorta tus direcciones web para promocionar tus propiedades y ver estadísticas'
            ],
            [
                'type' => 'link',
                'name' => 'Acceso al acortador de URLs',
                'url' => 'https://www.remax.net/shortener'
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
