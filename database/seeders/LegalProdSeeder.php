<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};

class LegalProdSeeder extends Seeder
{
    public function run(): void
    {
        $module = Module::updateOrCreate(
            ['name' => 'Legal RE/MAX'],
            ['image' => '/intranet/legal.jpg']
        );

        $page = Page::updateOrCreate(
            ['title' => 'Legal RE/MAX', 'module_id' => $module->id],
            ['image' => '/intranet/legal.jpg']
        );

        $module->update(['default_page_id' => $page->id]);

        foreach (['Administrador', 'Broker'] as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $module->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }
        $formPages = [
            'DECLARACIÓN BÚSQUEDA DE INMUEBLE' => '/legal/contratoBroker.aspx',
            'FORMULARIO DE VERIFICACION DE INTERMEDIARIOS.PDF' => '/legal/contratoBroker.aspx',
            'FORMULARIO DE ACTA DE ENTREGA DE DOCUMENTOS' => '/legal/contratoBroker.aspx',
            'FORMULARIO DE CAPTACIÓN' => '/legal/contratoBroker.aspx',
            'FORMULARIO DE INVENTARIO ESCRITO' => '/legal/contratoBroker.aspx',
            'FORMULARIO DE SOLICITUD DOCUMENTACIÓN DEL INQUILINO' => '/legal/contratoBroker.aspx',
        ];

        $formPageIds = [];

        foreach ($formPages as $title => $url) {
            $page = Page::updateOrCreate([
                'title' => $title,
                'module_id' => $module->id,
                'parent_id' => $page->id,

            ], [
                'image' => '/intranet/legal.jpg',
            ]);
            $formPageIds[] = ['type' => 'page', 'name' => $title, 'page_id' => $page->id];
        }

        $section = Section::updateOrCreate([
            'page_id' => $page->id,
            'title' => 'Formularios',
        ], [
            'order' => 2,
        ]);

        $order = 1;
        foreach ($formPageIds as $item) {
            Element::updateOrCreate([
                'section_id' => $section->id,
                'type' => 'page',
                'order' => $order++,
                'content' => [
                    'name' => $item['name'],
                    'page_id' => $item['page_id'],
                ],
            ]);
        }


        $secciones = [
            'Contratos' => [
                ['type' => 'link','name' => 'CONTRATO VENTA CON EXCLUSIVIDAD REMAX v01', 'url' => 'https://drive.google.com/file/d/1lumj-6omttevLYzNho0APwxBlEsY3Pu1/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO ALQUILER CON EXCLUSIVIDAD REMAX v01', 'url' => 'https://drive.google.com/file/d/1S-XZOlW-QoA60kuGECuSofXssh5mf0ir/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO ANTICRESIS CON EXCLUSIVIDAD REMAX v01', 'url' => 'https://drive.google.com/file/d/1HIMMJiIrmBWGuLdpX_OZjBR9tZXkYitQ/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO ALQUILER NO EXCLUSIVA REMAX v01', 'url' => 'https://drive.google.com/file/d/13xvt2A-qlGJdXkDDmmAPhNzV2FMeg6Q2/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO DE VENTA NO EXCLUSIVA REMAX v01', 'url' => 'https://drive.google.com/file/d/1nRRmtkNELIukglbjX30lOLmh99nedpBX/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO GESTION DE BUSQUEDA CON EXCLUSIVIDAD v01', 'url' => 'https://drive.google.com/file/d/1VJ547p0EKMTK2tmkkrW4VXdakl8zc9An/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO MODELO DE CONFIDENCIALIDAD PROVEEDORES v01', 'url' => 'https://drive.google.com/file/d/1rVcrUOtvJG9Y_9_bUYQ1fiuXlqrmZNIm/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO MODELO DE CONFIDENCIALIDAD TRABAJDOR FIJO v01', 'url' => 'https://drive.google.com/file/d/16e_F1eO7yZ5aHf-zj_9T-O8JnAgOfYGR/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO MODELO PARA AGENTES ASOCIADOS 02-2022', 'url' => 'https://drive.google.com/file/d/1NTnsJqSz87xyPb9a-8ymwNWBeOoFys08/view?usp=sharing'],
                ['type' => 'link','name' => 'CONTRATO RESPETO CLIENTE', 'url' => 'https://drive.google.com/file/d/1lH_zVyWMvywjluEt7TBG1ElPgdMADD6o/view?usp=sharing'],
            ],
            /*'Formularios' => [
                ['name' => 'DECLARACIÓN BÚSQUEDA DE INMUEBLE', 'url' => '/legal/contratoBroker.aspx'],
                ['name' => 'FORMULARIO DE VERIFICACION DE INTERMEDIARIOS.PDF', 'url' => '/legal/contratoBroker.aspx'],
                ['name' => 'FORMULARIO DE ACTA DE ENTREGA DE DOCUMENTOS', 'url' => '/legal/contratoBroker.aspx'],
                ['name' => 'FORMULARIO DE CAPTACIÓN', 'url' => '/legal/contratoBroker.aspx'],
                ['name' => 'FORMULARIO DE INVENTARIO ESCRITO', 'url' => '/legal/contratoBroker.aspx'],
                ['name' => 'FORMULARIO DE SOLICITUD DOCUMENTACIÓN DEL INQUILINO', 'url' => '/legal/contratoBroker.aspx'],
            ],*/
            'Requisitos Alquiler' => [
                ['type' => 'link','name' => 'Alquileres 50 y 50', 'url' => '/Sites/remaxbolivia/intranet/Varios/alquileres%2050_%20y%2050_.pdf'],
                ['type' => 'link','name' => 'ARTES CAPTACIÓN Q42020-02 ALQUILER', 'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-02%20ALQUILER.jpg'],
                ['type' => 'link','name' => 'ARTES CAPTACIÓN Q42020-03 ALQUILER', 'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-03%20ALQUILER.jpg'],
            ],
            'Requisitos Proyectos' => [
                ['type' => 'link','name' => 'ARTES CAPTACIÓN Q42020-04 PROYECTOS', 'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-04%20PROYECTOS.jpg'],
                ['type' => 'link','name' => 'REQUISITOS PARA LA ALTA DE PROYECTOS', 'url' => '/Sites/remaxbolivia/intranet/Varios/REQUISITOS%20PARA%20LA%20ALTA%20DE%20PROYECTOS.jpeg'],
                ['type' => 'link','name' => 'Plantilla Proyectos', 'url' => '/Sites/remaxbolivia/intranet/Documents/PLANTILLA%20PROYECTOS.pptx'],
                ['type' => 'link','name' => 'Especificaciones Logo Proyecto', 'url' => '/Sites/remaxbolivia/intranet/Documents/ESPECIFICACIONES%20LOGO%20PROYECTO.pptx'],
            ],
            'Requisitos Ventas' => [
                ['type' => 'link','name' => 'ARTES CAPTACIÓN Q42020-01 VENTA', 'url' => '/Sites/remaxbolivia/intranet/Varios/ARTES%20CAPTACIÓN%20Q42020-01%20VENTA.jpg'],
            ],
            'Varios' => [
                ['type' => 'link','name' => 'ACTA DE ENTREGA DE DOCUMENTOS', 'url' => '/Sites/remaxbolivia/intranet/Varios/ACTA%20DE%20ENTREGA%20DE%20DOCUMENTOS.docx'],
                ['type' => 'link','name' => 'FORMULARIO DE CAPTACIÓN', 'url' => '/Sites/remaxbolivia/intranet/Varios/FORMULARIO%20DE%20CAPTACI%C3%93N.docx'],
                ['type' => 'link','name' => 'INVENTARIO ESCRITO', 'url' => '/Sites/remaxbolivia/intranet/Varios/INVENTARIO%20ESCRITO.docx'],
                ['type' => 'link','name' => 'SOLICITUD DOCUMENTACIÓN DEL INQUILINO', 'url' => '/Sites/remaxbolivia/intranet/Varios/SOLICITUD%20DOCUMENTACI%C3%93N%20DEL%20INQUILINO.docx'],
            ]
        ];

        $order = 1;
        foreach ($secciones as $title => $elementos) {
            $section = Section::updateOrCreate(
                ['page_id' => $page->id, 'title' => $title],
                ['order' => $order++]
            );
            $elementOrder = 1;
            foreach ($elementos as $item) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $item['type'],
                    'order' => $elementOrder++,
                    'content' => [
                        'name' => $item['name'],
                        'url' => $item['url'],
                    ],
                ]);
            }
        }
    }
}
