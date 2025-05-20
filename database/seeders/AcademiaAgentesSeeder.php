<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};


class AcademiaAgentesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear módulo Universidad RE/MAX
        $module = Module::updateOrCreate([
            'name' => 'Academia RE/MAX'
        ], [
            'image' => 'intranet/ProtocoloBioseguridad.jpg',
            //'description' => 'Recursos de formación para agentes RE/MAX'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Universidad RE/MAX',
            'module_id' => $module->id,
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (Agentes y Administradores)
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

        // Sección de Videos
        $sectionVideos = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Videos de Formación',
        ], [
            'order' => 1,
        ]);

        $videos = [
            [
                'type' => 'video',
                'name' => 'NOVEDADES Análisis comparativo de mercado',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20Analisis%20comparativo%20de%20mercado.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES Base de datos Contactos',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20Base%20de%20datos%20Contactos.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES coincidencia de comprador',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20coincidencia%20de%20comprador.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES Conectar - Agent Connect',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20Conectar%20-%20Agent%20Connect.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES Nuevo Pipeline - Flujo de información',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20Nuevo%20Pipeline%20-Flujo%20de%20informacion.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES Panel de control - Tablero',
                'url' => '/Sites/remaxbolivia/intranet/Videos/NOVEDADES%20Panel%20de%20control%20-%20Tablero.mp4'
            ],
            [
                'type' => 'video',
                'name' => 'NOVEDADES Virtual open house',
                'url' => '/Sites/remaxbolivia/intranet/Videos/virtual%20open%20house.mp4'
            ]
        ];

        foreach ($videos as $index => $video) {
            Element::updateOrCreate([
                'section_id' => $sectionVideos->id,
                'type' => $video['type'],
                'order' => $index + 1,
            ], [
                'content' => [
                    'name' => $video['name'],
                    'url' => $video['url']
                ]
            ]);
        }

        // Sección de Sistema iList
        $sectionIList = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'SISTEMA ILIST',
        ], [
            'order' => 2,
        ]);

        $ilistDocs = [
            [
                'type' => 'presentation',
                'name' => 'Aclarativa del Tot m²=Terr+Const vA03',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Aclarativa%20del%20Tot%20m%C2%B2=Terr+Const%20vA03.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'ACM',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/ACM.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'Cargado de propiedad y Flujo de Contactos vA01',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Cargado%20de%20propiedad%20y%20Flujo%20de%20Contactos%20vA01.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'iMarketing Facebook',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/iMarketing%20Facebook.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'Presentacion Dashboard Agente y Agent Connect',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Presentacion%20Dashboard%20Agente%20y%20Agent%20Connect.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'Presentacion Referidos',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Presentacion%20Referidos.pptx'
            ],
            [
                'type' => 'presentation',
                'name' => 'Novedades Tipos de TRR Simplificado',
                'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Novedaddes%20Tipos%20de%20TRR%20%20Simplificado.pptx'
            ]
        ];

        foreach ($ilistDocs as $index => $doc) {
            Element::updateOrCreate([
                'section_id' => $sectionIList->id,
                'type' => $doc['type'],
                'order' => $index + 1,
            ], [
                'content' => [
                    'name' => $doc['name'],
                    'url' => $doc['url']
                ]
            ]);
        }

        // Sección de MAXCenter
        $sectionMaxcenter = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'MAXCENTER',
        ], [
            'order' => 3,
        ]);

        $maxcenterDocs = [
            [
                'type' => 'tutorial',
                'name' => 'Registro a Maxcenter',
                'url' => '/Sites/remaxbolivia/intranet/Documents/01%20Registro%20a%20Maxcenter.ppsx'
            ],
            [
                'type' => 'tutorial',
                'name' => 'Recuperación de contraseña MAXCenter',
                'url' => '/Sites/remaxbolivia/intranet/Documents/02%20Recuperacion%20de%20contrase%C3%B1a%20MAXCenter.ppsx'
            ]
        ];

        foreach ($maxcenterDocs as $index => $doc) {
            Element::updateOrCreate([
                'section_id' => $sectionMaxcenter->id,
                'type' => $doc['type'],
                'order' => $index + 1,
            ], [
                'content' => [
                    'name' => $doc['name'],
                    'url' => $doc['url']
                ]
            ]);
        }
    }
}
