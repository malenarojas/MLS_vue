<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};


class LegalAgentesProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener el módulo padre "Eventos para Agentes"
        $modulePadre = Module::where('name', 'Eventos para Agentes RE/MAX')->firstOrFail();

        // 2. Obtener la página padre "Eventos para Agentes"
        $pagePadre = Page::where('title', 'Eventos para Agentes')
                        ->where('module_id', $modulePadre->id)
                        ->firstOrFail();

        // 3. Crear página hija "Legal" (recursiva)
        $pageLegal = Page::updateOrCreate([
            'title' => 'Legal',
            'module_id' => $modulePadre->id,
            'parent_id' => $pagePadre->id
        ], [
            'image' => '/intranet/legal-agentes.jpg',
            'description' => 'Documentos legales y contratos para agentes'
        ]);

        // 4. Crear secciones y elementos para la página Legal
        $seccionesLegal = [
            'Contratos' => [
                [
                    'type' => 'image',
                    'name' => 'Imagen Contratos',
                    'url' => '/Sites/remaxbolivia/intranet/Calendario/PHOTO-2021-01-28-12-54-24.jpg',
                    'description' => 'Imagen representativa de contratos',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO ANTICRESIS CON EXCLUSIVIDAD REMAX v01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20ANTICRESIS%20CON%20EXCLUSIVIDAD%20REMAX%20v01.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO GESTION DE BUSQUEDA CON EXCLUSIVIDAD v01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20GESTION%20DE%20BUSQUEDA%20CON%20EXCLUSIVIDAD%20v01.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO MODELO DE CONFIDENCIALIDAD PROVEEDORES v01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20MODELO%20DE%20CONFIDENCIALIDAD%20PROVEEDORES%20v01.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO MODELO DE CONFIDENCIALIDAD TRABAJDOR FIJO v01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20MODELO%20DE%20CONFIDENCIALIDAD%20TRABAJDOR%20FIJO%20v01.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO MODELO PARA AGENTES ASOCIADOS V01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20MODELO%20PARA%20AGENTES%20ASOCIADOS%20V01.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO RESPETO CLIENTE',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20RESPETO%20CLIENTE.doc',
                ],
                [
                    'type' => 'link',
                    'name' => 'CONTRATO VENTA CON EXCLUSIVIDAD REMAX v01',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/CONTRATO%20VENTA%20CON%20EXCLUSIVIDAD%20REMAX%20v01.doc',
                ]
            ],
            'Requisitos Alquiler' => [
                [
                    'type' => 'link',
                    'name' => 'Alquileres 50 y 50',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/alquileres%2050_%20y%2050_.pdf',
                ],
                [
                    'type' => 'link',
                    'name' => 'ARTES CAPTACIÓN Q42020-02 ALQUILER',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-02%20ALQUILER.jpg',
                ],
                [
                    'type' => 'link',
                    'name' => 'ARTES CAPTACIÓN Q42020-03 ALQUILER',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-03%20ALQUILER.jpg',
                ]
            ],
            'Requisitos Proyectos' => [
                [
                    'type' => 'link',
                    'name' => 'ARTES CAPTACIÓN Q42020-04 PROYECTOS',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-04%20PROYECTOS.jpg',
                ],
                [
                    'type' => 'link',
                    'name' => 'REQUISITOS PARA LA ALTA DE PROYECTOS',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/REQUISITOS%20PARA%20LA%20ALTA%20DE%20PROYECTOS.jpeg',
                ]
            ],
            'Requisitos Ventas' => [
                [
                    'type' => 'link',
                    'name' => 'ARTES CAPTACIÓN Q42020-01 VENTA',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-01%20VENTA.jpg',
                ]
            ],
            'Varios' => [
                [
                    'type' => 'link',
                    'name' => 'ACTA DE ENTREGA DE DOCUMENTOS',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/ACTA%20DE%20ENTREGA%20DE%20DOCUMENTOS.docx',
                ],
                [
                    'type' => 'link',
                    'name' => 'FORMULARIO DE CAPTACIÓN',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/FORMULARIO%20DE%20CAPTACI%C3%93N.docx',
                ],
                [
                    'type' => 'link',
                    'name' => 'INVENTARIO ESCRITO',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/INVENTARIO%20ESCRITO.docx',
                ],
                [
                    'type' => 'link',
                    'name' => 'PLAN DE MKT',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/PLAN%20DE%20MKT.pdf',
                ],
                [
                    'type' => 'link',
                    'name' => 'SOLICITUD DOCUMENTACIÓN DEL INQUILINO',
                    'url' => '/Sites/remaxbolivia/intranet/Varios/SOLICITUD%20DOCUMENTACI%C3%93N%20DEL%20INQUILINO.docx',
                ]
            ]
        ];

        // 5. Crear secciones y elementos
        $order = 1;
        foreach ($seccionesLegal as $titulo => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $pageLegal->id,
                'title' => $titulo,
            ], [
                'order' => $order++,
            ]);

            $elementOrder = 1;
            foreach ($elementos as $elemento) {
                $content = [
                    'name' => $elemento['name'] ?? null,
                    'description' => $elemento['description'] ?? null
                ];

                if ($elemento['type'] === 'image') {
                    $content['url'] = $elemento['url'];
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
