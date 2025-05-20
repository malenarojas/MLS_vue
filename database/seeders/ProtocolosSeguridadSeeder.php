<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};


class ProtocolosSeguridadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear o actualizar el módulo Protocolos de Seguridad
         $module = Module::updateOrCreate([
            'name' => 'Protocolos de Seguridad Sanitaria para Agentes RE/MAX Bolivia'
        ], [
            'image' => 'intranet/ProtocoloBioseguridad.jpg',
            //'description' => 'Certificación y protocolos de seguridad sanitaria para oficinas, agentes y clientes'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Certificación Protocolo de Seguridad Sanitaria RE/MAX Bolivia',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/PolíticaCompañía.jpg',
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (para todos los roles)
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

        // Sección de Video de Certificación
        $sectionVideo = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Video de Certificación',
        ], [
            'order' => 1,
        ]);

        Element::updateOrCreate([
            'section_id' => $sectionVideo->id,
            'type' => 'link',
            'order' => 1,
        ], [
            'content' => [
                'type' => 'link',
                'name' => 'Video de Certificación en Protocolo de Bioseguridad',
                'url' => 'https://drive.google.com/file/d/18FirLGq3ySvTXhbzRgzufqtVM_9TZ88d/view?usp=sharing',
                'description' => 'Video explicativo sobre los protocolos de bioseguridad',
            ]
        ]);

        // Sección de Protocolos
        $sectionProtocolos = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Protocolos',
        ], [
            'order' => 2,
        ]);

        $protocolos = [
            [
                'type' => 'link',
                'name' => 'Protocolo de Seguridad Sanitaria ante el Covid-19',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/RB%2021%20PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20ANTE%20EL%20COVID-19.pdf',
                'description' => 'Protocolo general para prevención del COVID-19'
            ],
            [
                'type' => 'link',
                'name' => 'Protocolo de Seguridad Sanitaria para Aperturar Oficinas Franquiciadas RE/MAX',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20APERTURAR%20OFICINAS%20REMAX.pdf',
                'description' => 'Protocolo específico para apertura de oficinas'
            ],
            [
                'type' => 'link',
                'name' => 'Protocolo de Seguridad Sanitaria para Agentes Asociados RE/MAX en casos de visitas a inmuebles',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20AGENTES%20REMAX.pdf',
                'description' => 'Protocolo para agentes durante visitas a propiedades'
            ],
            [
                'type' => 'link',
                'name' => 'Protocolo de Seguridad Sanitaria para Clientes Propietarios',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20CLIENTES%20PROPIETARIOS.pdf',
                'description' => 'Recomendaciones para clientes propietarios'
            ],
            [
                'type' => 'link',
                'name' => 'Protocolo de Seguridad Sanitaria para Clientes Compradores',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/PROTOCOLO%20DE%20SEGURIDAD%20SANITARIA%20PARA%20CLIENTES%20COMPRADORES.pdf',
                'description' => 'Recomendaciones para clientes compradores'
            ]
        ];

        foreach ($protocolos as $index => $protocolo) {
            Element::updateOrCreate([
                'section_id' => $sectionProtocolos->id,
                'type' => 'link',
                'order' => $index + 1,
            ], [
                'content' => array_merge($protocolo, [
                ])
            ]);
        }

        // Sección de Formularios
        $sectionFormularios = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Formularios',
        ], [
            'order' => 3,
        ]);

        $formularios = [
            [
                'type' => 'link',
                'name' => 'Formulario de Limpieza y Desinfección del Inmueble COVID-19',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/FORM%20REGISTRO%20DE%20LIMPIEZA%20Y%20DESINFECCIÓN%20COVID-19v01.pdf',
                'description' => 'Registro de limpieza y desinfección de inmuebles'
            ],
            [
                'type' => 'link',
                'name' => 'Formulario Registro de Visita COVID-19',
                'url' => '/Sites/remaxbolivia/intranet/PROTOCOLO/FORM%20REGISTRO%20DE%20VISITA%20COVID-19v01.pdf',
                'description' => 'Registro de visitas a inmuebles durante la pandemia'
            ]
        ];

        foreach ($formularios as $index => $formulario) {
            Element::updateOrCreate([
                'section_id' => $sectionFormularios->id,
                'type' => 'link',
                'order' => $index + 1,
            ], [
                'content' => array_merge($formulario, [

                ])
            ]);
        }

        // Sección de Declaración Jurada
        $sectionDeclaracion = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Declaración Jurada',
        ], [
            'order' => 4,
        ]);

        Element::updateOrCreate([
            'section_id' => $sectionDeclaracion->id,
            'type' => 'link',
            'order' => 1,
        ], [
            'content' => [
                'type' => 'link',
                'name' => 'Formulario de Certificación Protocolo de Seguridad',
                'url' => 'https://zfrmz.com/TGmDUe2TnGUFJDpBiNEw',
                'description' => 'Declaración jurada de cumplimiento de protocolos',
            ]
        ]);
    }
}
