<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};

class SistemasProdSeeder extends Seeder
{
    public function run(): void
    {
        $modulesistemas = Module::updateOrCreate([
            'name' => 'Sistemas RE/MAX'
        ], [
            'image' => '/intranet/Tecnología.jpg'
        ]);

        $pagesistemas = Page::updateOrCreate([
            'title' => 'Sistemas RE/MAX',
            'module_id' => $modulesistemas->id,
        ], [
            'image' => '/intranet/Tecnología.jpg',
        ]);
        $modulesistemas->update(['default_page_id' => $pagesistemas->id]);

        $rolesPermitidos = ['Administrador', 'Broker'];
        foreach ($rolesPermitidos as $role) {
            AccessRule::updateOrCreate([
                'resource_type' => Module::class,
                'resource_id' => $modulesistemas->id,
                'type' => 'role',
                'value' => $role,
                'access_type' => 'view',
            ]);
        }

        $secciones = [
            'VIDEOS' => [
                ['type' => 'link','name' => 'Video cambio de permiso agente.mp4', 'url' => 'https://drive.google.com/file/d/1dss3Yp4j3JeAZQ4_i8u4EqXpEJULmesL/view?usp=drive_link'],
                ['type' => 'link','name' => 'Cargar Contactos desde Excel a iList', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/%60Cargar%20contactos%20iList.mp4'],
                ['type' => 'link','name' => 'Archivo excel de ejemplo', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/contactosSubir.xlsx'],
                ['type' => 'link','name' => 'Virtual open house', 'url' => '/Sites/remaxbolivia/intranet/Videos/virtual%20open%20house.mp4'],
                ],
            'V/TEC' => [
                ['type' => 'link','name' => 'Como Creear un TRR en ilist 10', 'url' => 'https://drive.google.com/file/d/1ah6ZP3vqza46TEfSPJq50gbTtEeyqWXN/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 19 de agosto 2022 Tablero', 'url' => 'https://drive.google.com/file/d/1EsOAnG_DCdTq5wDBbg7GaXa_eAiIqxgz/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 26 de agosto 2022 Contactos', 'url' => 'https://drive.google.com/file/d/1HJe3fovAUiuStmGXR7-mQtfHHLIu-Y8e/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 02 de septiembre 2022 Busqueda dinamica', 'url' => 'https://drive.google.com/file/d/1AvwMPcqsRm6MkhCVkgqL0fri0O3m1o0r/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 09 de septiembre 2022 Cargado de Propiedades', 'url' => 'https://drive.google.com/file/d/19JQgc7ve6zHChHpVsBTx-oo-78axGX-3/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 16 de septiembre 2022 Cargado de Propiedades Extra', 'url' => 'https://drive.google.com/file/d/1mYD6nMUYNWmsYpGa7utw8ebbO9r9Qzj7/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 23 de septiembre 2022 Cargado correcto de Propiedades I', 'url' => 'https://drive.google.com/file/d/1aDAspEOjr-Am80GHvvk1UsUMZ98rMED8/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 07 de Octubre 2022 Cargado correcto de Propiedades II', 'url' => 'https://drive.google.com/file/d/1VsA8zl80z2_OF7P3TamL6p7fl8PvPZHj/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 14 de Octubre 2022 ACM', 'url' => 'https://drive.google.com/file/d/153cvepE11hBewXf-36oCBhJk07Q7Xukz/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 21 de Octubre 2022 Referido', 'url' => 'https://drive.google.com/file/d/1K6isaqeRWqMrk_yBRb5SHeBuoUaTersl/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 24 de Octubre 2022 ACM', 'url' => 'https://drive.google.com/file/d/15-uzmaUnihILPuhYL3RyWHckcQaLp32r/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 26 de Octubre 2022 Busqueda Dinamica', 'url' => 'https://drive.google.com/file/d/1vPXflrb1gvtVr35EJIPNOD8UfQcZLOPT/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 28 de Octubre 2022 Compartir propiedad', 'url' => 'https://drive.google.com/file/d/1gBJaRdvlcoBWMKvdKOfV-VJhlgDOVFDO/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 03 de Noviembre 2022 Novedades del sistema iMarketing', 'url' => 'https://drive.google.com/file/d/1B4jDEuhUW0gW7aXcFVOnwkmzVjSM21mg/view?usp=sharing'],
                ['type' => 'link','name' => 'V/TEC 04 de Noviembre 2022 Contactos', 'url' => 'https://drive.google.com/file/d/1fxXXCQPw7IhCZ9ucOjRByou7vS03pDcn/view?usp=sharing'],
            ],

            'CAPACITACIONES TEMATICAS' => [
                ['type' => 'link','name' => 'CAPACITACIÓN MARKETING & PHOTOFY', 'url' => 'https://drive.google.com/file/d/1XYX_81SJFrsIryhlXW-E4kW9MHCKaJQa/view?usp=sharing'],
                ['type' => 'link','name' => 'Potenciando tus Prospectos en ILIST', 'url' => 'https://drive.google.com/file/d/18XSjvHAu1sJosBRH9vlrk6BgHa-hlMo2/view?usp=sharing'],
                ['type' => 'link','name' => 'Power Whats Possible', 'url' => 'https://drive.google.com/file/d/1Hx0KMHLN71PPzyqnjsAhLDL1zTTa9VrW/view?usp=sharing'],
                ['type' => 'link','name' => 'Manejo De Marca RE/MAX Bolivia', 'url' => 'https://drive.google.com/file/d/17Eku0ypip3e--C8CplZVyWjuAOU4-Ouc/view?usp=sharing'],
                ['type' => 'link','name' => 'Como Ser Crack En Tus Redes Sociales', 'url' => 'https://drive.google.com/file/d/1DHWZwXVNJzJyLPvrnDxdHDC09NR4udfs/view?usp=sharing'],
                ['type' => 'link','name' => 'Contabilidad Inmobiliaria', 'url' => 'https://drive.google.com/file/d/199h_x7mgD0b0Nsnqb8Sx7GxX8I0IF6Vw/view?usp=sharing'],
                ['type' => 'link','name' => 'Analisis Comparativo de Mercado KNOW HOW', 'url' => 'https://drive.google.com/file/d/1KDmctNt9OzSpKCMzLcJvlFLXSFDAE-1b/view?usp=sharing'],
                ['type' => 'link','name' => 'ABC Legal', 'url' => 'https://drive.google.com/file/d/10XZkAoJv0DHSfePNAzpMkwxG_BA7Fx3v/view?usp=sharing'],
                ['type' => 'link','name' => 'Agentes Top 100', 'url' => 'https://drive.google.com/file/d/10yUaEqsO8JOuLbAnQXwHd3YiWJmTOJMX/view?usp=sharing'],
                ['type' => 'link','name' => 'Especialista en Bienes Raíces Residenciales', 'url' => 'https://drive.google.com/file/d/1iKz02x2bt8fLwwmH9J2SWsd4SbYZOCvo/view?usp=sharing'],
            ],
            'SISTEMA ILIST' => [
                ['type' => 'link','name' => 'Aclarativa del Tot m²=Terreno + Construcción.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Aclarativa%20del%20Tot%20m%C2%B2=Terr+Const%20vA03.pptx'],
                ['type' => 'link','name' => 'ACM Analisis Comparativo de Mercado.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/ACM.pptx'],
                ['type' => 'link','name' => 'Cargado de propiedad y Flujo de Contactos.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Cargado%20de%20propiedad%20y%20Flujo%20de%20Contactos%20vA01.pptx'],
                ['type' => 'link','name' => 'iMarketing Facebook.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/iMarketing%20Facebook.pptx'],
                ['type' => 'link','name' => 'Presentacion Dashboard Agente y Agent Connect.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Presentacion%20Dashboard%20Agente%20y%20Agent%20Connect.pptx'],
                ['type' => 'link','name' => 'Presentacion Referidos.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Presentacion%20Referidos.pptx'],
                ['type' => 'link','name' => 'Novedaddes Tipos de TRR Simplificado.pptx', 'url' => '/Sites/remaxbolivia/intranet/SistemaiList/Novedaddes%20Tipos%20de%20TRR%20%20Simplificado.pptx'],
            ],
            'MAXCENTER' => [
                ['type' => 'link','name' => 'Registro a Maxcenter.ppsx', 'url' => '/Sites/remaxbolivia/intranet/Documents/01%20Registro%20a%20Maxcenter.ppsx'],
                ['type' => 'link','name' => 'Recuperacion de contraseña MAXCenter.ppsx', 'url' => '/Sites/remaxbolivia/intranet/Documents/02%20Recuperacion%20de%20contrase%C3%B1a%20MAXCenter.ppsx'],
            ],
        ];
        $order = 1;

        foreach ($secciones as $titulo => $elementos) {
            $section = Section::updateOrCreate([
                'page_id' => $pagesistemas->id,
                'title' => $titulo,
            ],[
                'order' => $order++,
            ]);

            $elementOrder = 1;
            foreach ($elementos as $elemento) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $elemento['type'],
                    'order' => $elementOrder++,
                    'content' => $elemento['type'] === 'page' ? [
                        'name' => $elemento['name'],
                        'page_id' => $elemento['page_id'],
                    ] : [
                        'name' => $elemento['name'],
                        'url' => $elemento['url'],
                    ],
                ]);
            }
        }

    }
}
