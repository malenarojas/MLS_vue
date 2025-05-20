<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};


class TecnologiaAgentesProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el módulo Tecnología para Agentes
        $module = Module::updateOrCreate([
            'name' => 'Tecnología para Agentes RE/MAX'
        ], [
            'image' => '/intranet/tecnologia-agentes.jpg'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Tecnología para Agentes',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/tecnologia-agentes.jpg',
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (solo para Agentes y Administradores)
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
            'Photofy para Agentes' => [
                [
                    'type' => 'image',
                    'name' => 'Logo Photofy',
                    'url' => '/Sites/remaxbolivia/intranet/tecnologia/logoPhotofy.png',
                    'style' => 'width: 800px; height: 246px;',
                    'description' => 'Para generar publicaciones como un profesional'
                ],
                [
                    'type' => 'link',
                    'name' => 'Acceso a Photofy',
                    'url' => 'https://resources.remax.net/products/photofy',
                    'description' => 'Plataforma para crear publicaciones profesionales'
                ],
                [
                    'type' => 'link',
                    'name' => 'Tutorial: Cómo Instalar Photofy',
                    'url' => '/Sites/remaxbolivia/intranet/tecnologia/C%C3%B3mo%20Instalar%20Photofy%20para%20Agentes%20RE_MAX.%20Tutorial%20paso%20a%20paso.mp4',
                    'description' => 'Video paso a paso para configuración inicial'
                ]
            ],
            'MAXCenter para Agentes' => [
                [
                    'type' => 'image',
                    'name' => 'MAXCenter',
                    'url' => '/Sites/remaxbolivia/intranet/tecnologia/maxcenter.jpg',
                    'style' => 'width: 800px; height: 246px;',
                    'description' => 'Plataforma para descargar recursos y compartir mejores prácticas'
                ],
                [
                    'type' => 'link',
                    'name' => 'Acceso a MAXCenter',
                    'url' => 'https://www.remax.net/',
                    'description' => 'Portal principal de MAXCenter'
                ],
                [
                    'type' => 'link',
                    'name' => 'Guía de Registro',
                    'url' => '/Sites/remaxbolivia/intranet/Documents/01%20Registro%20a%20Maxcenter.ppsx',
                    'description' => 'Primeros pasos en MAXCenter'
                ],
                [
                    'type' => 'link',
                    'name' => 'Recuperación de contraseña',
                    'url' => '/Sites/remaxbolivia/intranet/Documents/02%20Recuperacion%20de%20contrase%C3%B1a%20MAXCenter.ppsx',
                    'description' => 'Proceso para recuperar acceso'
                ]
            ],
            'RE/MAX Hustle' => [
                [
                    'type' => 'image',
                    'name' => 'RE/MAX Hustle',
                    'url' => '/Sites/remaxbolivia/intranet/tecnologia/remaxHustle.jpg',
                    'style' => 'width: 520px; height: 212px;',
                    'description' => 'Soluciones de marketing llave en mano'
                ],
                [
                    'type' => 'link',
                    'name' => 'Acceso a RE/MAX Hustle',
                    'url' => 'https://www.remaxhustle.com/',
                    'description' => 'Marketing profesional para agentes'
                ]
            ],
            'Herramientas Digitales' => [
                [
                    'type' => 'image',
                    'name' => 'Acortador de URLs',
                    'url' => '/Sites/remaxbolivia/intranet/tecnologia/urlSortener.jpg',
                    'style' => 'width: 293px; height: 90px;',
                    'description' => 'Acorta tus direcciones web para promocionar propiedades'
                ],
                [
                    'type' => 'link',
                    'name' => 'Acceso al acortador de URLs',
                    'url' => 'https://www.remax.net/shortener',
                    'description' => 'Herramienta para links cortos y seguimiento'
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
                    'url' => $elemento['url'],
                    'description' => $elemento['description'] ?? null
                ];

                // Agregar estilo si está definido
                if (isset($elemento['style'])) {
                    $content['style'] = $elemento['style'];
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
