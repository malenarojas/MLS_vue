<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};


class PoliticasAgentesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el módulo de Políticas para Agentes
        $module = Module::updateOrCreate([
            'name' => 'Políticas para Agentes RE/MAX'
        ], [
            'image' => '/intranet/ProtocoloBioseguridad.jpg',
            //'description' => 'Manuales, formularios y glosario para agentes RE/MAX'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Políticas para Agentes',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/ProtocoloBioseguridad.jpg',
            'route' => '/agentes/politicas'
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (para Agentes y Administradores)
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

        // Sección de Manuales
        $sectionManuales = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Manuales',
        ], [
            'order' => 1,
        ]);

        Element::updateOrCreate([
            'section_id' => $sectionManuales->id,
            'type' => 'link',
            'order' => 1,
        ], [
            'content' => [
                'type' => 'link',
                'name' => 'MANUAL RE/MAX PARA EL AGENTE',
                'url' => 'https://drive.google.com/file/d/1mfeReY3V1mjtxPRsgx8YplvKYfF80vog/view?usp=sharing',
                'description' => 'Manual completo para agentes RE/MAX',
            ]
        ]);

        // Sección de Formularios
        $sectionFormularios = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Formularios',
        ], [
            'order' => 2,
        ]);

        $formularios = [
            [
                'type' => 'link',
                'name' => 'RB 12 FORMULARIO DE COMPRA DE SOUVENIRS RB.MKT -01',
                'url' => '/Sites/remaxBolivia/intranet/Documents/RB%2012%20Formulario%20de%20compra%20de%20Souvenirs%20RB.MKT%20-01.xlsx',
                'description' => 'Formulario para solicitud de materiales promocionales'
            ],
            [
                'type' => 'link',
                'name' => 'SOLICITUD DOCUMENTACIÓN DEL INQUILINO',
                'url' => '/Sites/remaxBolivia/intranet/Documents/SOLICITUD%20DOCUMENTACIO%CC%81N%20DEL%20INQUILINO.docx',
                'description' => 'Formulario para recopilación de documentación de inquilinos'
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

        // Sección de Glosario
        $sectionGlosario = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Glosario',
        ], [
            'order' => 3,
        ]);

        Element::updateOrCreate([
            'section_id' => $sectionGlosario->id,
            'type' => 'link',
            'order' => 1,
        ], [
            'content' => [
                'name' => '2019 RE/MAX GLOSSARY DIGITAL',
                'url' => '/Sites/remaxBolivia/intranet/Documents/2019-ReMax-Glossary-DIGITAL.pdf',
                'description' => 'Glosario de términos inmobiliarios RE/MAX',
            ]
        ]);
    }
}
