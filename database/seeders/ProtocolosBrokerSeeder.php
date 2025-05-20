<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};


class ProtocolosBrokerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear módulo
        $module = Module::updateOrCreate([
            'name' => 'Protocolos de Seguridad para Brokers'
        ], [
            'image' => 'intranet/ProtocoloBioseguridad.jpg',
           // 'description' => 'Protocolos y formularios de seguridad sanitaria'
        ]);

        // Crear página
        $page = Page::updateOrCreate([
            'title' => 'Certificación Protocolo de Seguridad Sanitaria RE/MAX Bolivia',
            'module_id' => $module->id,
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Permisos
        AccessRule::updateOrCreate([
            'resource_type' => Module::class,
            'resource_id' => $module->id,
            'type' => 'role',
            'value' => 'Administrador',
            'access_type' => 'view',
        ]);

        AccessRule::updateOrCreate([
            'resource_type' => Module::class,
            'resource_id' => $module->id,
            'type' => 'role',
            'value' => 'Broker',
            'access_type' => 'view',
        ]);

        // Secciones y elementos
        $sections = [
            [
                'title' => 'Video de Certificación',
                'elements' => [
                    [
                        'type' => 'link',
                        'name' => 'Video de Certificación en Protocolo de Bioseguridad',
                        'url' => 'https://drive.google.com/file/d/18FirLGq3ySvTXhbzRgzufqtVM_9TZ88d/view?usp=sharing',
                        'description' => 'Video explicativo sobre protocolos'
                    ]
                ]
            ],
            [
                'title' => 'Protocolos',
                'elements' => [
                    [
                        'type' => 'link',
                        'name' => 'Protocolo de Seguridad Sanitaria ante el Covid-19',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/RB%2021%20PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20ANTE%20EL%20COVID-19.pdf'
                    ],
                    [
                        'type' => 'link',
                        'name' => 'Protocolo para Aperturar Oficinas Franquiciadas',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20APERTURAR%20OFICINAS%20REMAX.pdf'
                    ],
                    [
                        'type' => 'link',
                        'name' => 'Protocolo para Agentes en visitas a inmuebles',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20AGENTES%20REMAX.pdf'
                    ],
                    [
                        'type' => 'link',
                        'name' => 'Protocolo para Clientes Propietarios',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20CLIENTES%20PROPIETARIOS.pdf'
                    ],
                    [
                        'type' => 'link',
                        'name' => 'Protocolo para Clientes Compradores',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20CLIENTES%20COMPRADORES.pdf'
                    ]
                ]
            ],
            [
                'title' => 'Formularios',
                'elements' => [
                    [
                        'type' => 'link',
                        'name' => 'Formulario de Limpieza y Desinfección',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/FORM%20REGISTRO%20DE%20LIMPIEZA%20Y%20DESINFECCIÓN%20COVID-19v01.pdf'
                    ],
                    [
                        'type' => 'link',
                        'name' => 'Formulario Registro de Visitas',
                        'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/FORM%20REGISTRO%20DE%20VISITA%20COVID-19v01.pdf'
                    ]
                ]
            ],
            [
                'title' => 'Declaración Jurada',
                'elements' => [
                    [
                        'type' => 'link',
                        'name' => 'Formulario de Certificación Protocolo de Seguridad',
                        'url' => 'https://zfrmz.com/TGmDUe2TnGUFJDpBiNEw'
                    ]
                ]
            ]
        ];

        // Crear secciones y elementos
        $sectionOrder = 1;
        foreach ($sections as $sectionData) {
            $section = Section::updateOrCreate([
                'page_id' => $page->id,
                'title' => $sectionData['title'],
            ], [
                'order' => $sectionOrder++
            ]);

            $elementOrder = 1;
            foreach ($sectionData['elements'] as $elementData) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $elementData['type'],
                    'order' => $elementOrder++,
                ], [
                    'content' => [
                        'name' => $elementData['name'],
                        'url' => $elementData['url'],
                        'description' => $elementData['description'] ?? null
                    ]
                ]);
            }
        }
    }
}
