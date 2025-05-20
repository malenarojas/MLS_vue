<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};

class PoliticasCompaniaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el módulo de Políticas
        $module = Module::updateOrCreate([
            'name' => 'Políticas de Compañía RE/MAX Bolivia'
        ], [
            'image' => '/intranet/politicas-compania.jpg',
            'description' => 'Políticas, formularios y procedimientos administrativos de RE/MAX Bolivia'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Políticas de Compañía',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/politicas-compania.jpg',
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (para Brokers y Administradores)
        $rolesPermitidos = ['Administrador', 'Broker'];
        foreach ($rolesPermitidos as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $module->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }

        // Sección de Administración
        $sectionAdmin = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Administración',
        ], [
            'order' => 1,
        ]);

        Element::updateOrCreate([
            'section_id' => $sectionAdmin->id,
            'type' => 'link',
            'order' => 1,
        ], [
            'content' => [
                'name' => 'Administración (Reuniones, Procedimientos, etc.)',
                'url' => '/administracion/default.aspx',
                'description' => 'Acceso al área de administración',
                'icon' => '/Sites/remaxbolivia/intranet/Images/link.png'
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
                'name' => 'Formulario Cancelación de Captación.xlsx',
                'url' => 'https://docs.google.com/spreadsheets/d/11ODQ8WvZuI9DdR6Jgy2LERsdSDqViQza/edit?usp=drive_link&ouid=105331632790661606340&rtpof=true&sd=true',
                'description' => 'Formulario para cancelación de captaciones'
            ],
            [
                'type' => 'link',
                'name' => 'RRB 01 Formulario de Cambio de estado vA01.pdf',
                'url' => 'https://drive.google.com/file/d/1gdCXPY1ycZqoHQaGFqPMrzke063W1tF9/view?usp=share_link',
                'description' => 'Formulario para cambio de estado de propiedades'
            ],
            [
                'type' => 'link',
                'name' => 'RB.RU - 01 - Fases inicial, intermedia y avanzada.pdf',
                'url' => 'https://drive.google.com/file/d/1SqViDXAB2rnACTHviKRkiAHDfDc_7HaH/view?usp=share_link',
                'description' => 'Documento de fases del proceso inmobiliario'
            ],
            [
                'type' => 'link',
                'name' => 'RB 02 Formulario de Registro de Nuevos Miembros.pdf',
                'url' => '/Sites/remaxbolivia/intranet/formularios/RB_02_Formulario_de_Registro_de_Nuevos_Miembros.pdf',
                'description' => 'Formulario para registro de nuevos agentes'
            ],
            [
                'type' => 'link',
                'name' => 'Declaración Agentes Antecedentes',
                'url' => '/Sites/remaxbolivia/intranet/formularios/Declaracio%CC%81n%20Agentes%20Antecedentesfinal.docx',
                'description' => 'Formulario de declaración jurada de antecedentes'
            ],
            [
                'type' => 'link',
                'name' => 'FORMULARIO DE BAJA DE COMISION.pdf',
                'url' => 'https://drive.google.com/file/d/1GDK679VDlJ1vtG-dIaitxC4j4OCrZdt0/view?usp=sharing',
                'description' => 'Formulario para solicitud de baja de comisión'
            ],
            [
                'type' => 'link',
                'name' => 'RB 12 Formulario de compra de Souvenirs RB.MKT -01',
                'url' => '/Sites/remaxbolivia/intranet/Documents/RB%2012%20Formulario%20de%20compra%20de%20Souvenirs%20RB.MKT%20-01.xlsx',
                'description' => 'Solicitud de compra de materiales promocionales'
            ],
            [
                'type' => 'link',
                'name' => 'FORMULARIOS DE CIERRE CON EXTERNOS_v01.pdf',
                'url' => 'https://drive.google.com/file/d/1gKNnscIFoJLELcV4FV1w7YY53U1sHBqg/view?usp=share_link',
                'description' => 'Formularios para procesos de cierre con externos'
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
    }
}
