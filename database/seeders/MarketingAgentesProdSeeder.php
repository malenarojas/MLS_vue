<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};


class MarketingAgentesProdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar el módulo Marketing para Agentes
        $module = Module::updateOrCreate([
            'name' => 'Marketing Agentes RE/MAX'
        ], [
            'image' => '/intranet/marketing.jpg'
        ]);

        // Crear página principal
        $page = Page::updateOrCreate([
            'title' => 'Marketing para Agentes',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/marketing-agentes.jpg',
        ]);
        $module->update(['default_page_id' => $page->id]);

        // Asignar permisos (solo Administradores y Agentes)
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
            'Lanzamiento RE/MAXIMIZA' => [
                ['type' => 'link', 'name' => 'Lanzamiento RE/MAXIMIZA', 'url' => 'https://drive.google.com/drive/folders/1fIOauZUv_pDkOFR_Eo_N_adwL9ua2Cty?usp=sharing'],
                ['type' => 'link', 'name' => 'LOGOS REMAXIMIZA', 'url' => 'https://drive.google.com/drive/folders/1831yVN3berQiUSBTIHP_giUtnEdp8iKe?usp=sharing'],
            ],
            'BRANDING' => [
                ['type' => 'link', 'name' => 'ARTÍCULOS PROMOCIONALES REMAX BOLIVIA.pdf', 'url' => 'https://drive.google.com/file/d/1v-vSPhxinOYIov_h_uj59-oAVS8KdwiA/view?usp=sharing'],
                ['type' => 'link', 'name' => 'MATERIAL PUBLICITARIO.pdf', 'url' => 'https://drive.google.com/file/d/1XurMzyPzBFpt31tK21KV8g6csG2w4zRq/view?usp=sharing'],
                ['type' => 'link', 'name' => 'FORMATO TARJETAS PERSONALES.ai', 'url' => 'https://drive.google.com/file/d/1GQqP2v0ROIyR5ySdIY28-AV_YdQryDb3/view?usp=sharing'],
                ['type' => 'link', 'name' => 'LÍNEA GRÁFICA GENÉRICA PARA POWER POINT.ai', 'url' => 'https://drive.google.com/file/d/12f4A7O_D-vsc9mwiyu-YILD9r0RYwiX4/view?usp=sharing'],
                ['type' => 'link', 'name' => 'FUENTES.ai', 'url' => 'https://drive.google.com/drive/folders/1IuC5AcPcbiAN-VwxeoTdb6iXn7VhmxH5?usp=sharing'],
            ],
            'LOGOTIPOS' => [
                ['type' => 'link', 'name' => 'GENERALES', 'url' => 'https://drive.google.com/drive/folders/11oTJGq2rdlzK-ilmZTuaP-dbgrH-LvCY?usp=sharing'],
                ['type' => 'link', 'name' => 'PREMIACIONES', 'url' => 'https://drive.google.com/drive/folders/1vK1ckJub_mghzWXI1tn8YnWxaa_ekpOt?usp=sharing'],
            ],
            'MANUAL DE MARCA' => [
                ['type' => 'link', 'name' => 'MANUAL DE MARCA 2023', 'url' => 'https://drive.google.com/file/d/1uXcM86fvNGuIAR9sJD6w7Ovx1x5BQMQC/view?usp=sharing'],
                ['type' => 'link', 'name' => 'MANUAL DE MARCA 2023 TEAM.pdf', 'url' => 'https://drive.google.com/file/d/19qft4hWekpLSHtwYCLPZhN46mKR1Sm4v/view?usp=sharing'],
                ['type' => 'link', 'name' => 'MANUAL DE MARCA 2022 - LETREROS REMAX CON QR', 'url' => 'https://drive.google.com/file/d/14mZhMWS1oTZDI7WiX1pNxXUBlnvVMJud/view?usp=sharing'],
            ],
            'REDES SOCIALES' => [
                ['type' => 'link', 'name' => 'ARTES CORPORATIVOS PARA FACEBOOK E INSTAGRAM.ai', 'url' => 'https://drive.google.com/file/d/1y00Koi4cRKuxTVbv8LZvTq_QsznkxJ-N/view?usp=sharing'],
            ],
            'TIK TOK' => [
                ['type' => 'link', 'name' => 'Compradores', 'url' => 'https://drive.google.com/drive/folders/19RHBwK-Ky6cQU4Y6tfbwhOiXwxfVHwA9?usp=sharing'],
                ['type' => 'link', 'name' => 'Vendedores', 'url' => 'https://drive.google.com/drive/folders/1QqBJVF5o-AZoGEy6aI66UzdTxr85xIGh?usp=sharing'],
                ['type' => 'link', 'name' => 'Promocion', 'url' => 'https://drive.google.com/drive/folders/1DmooE8GK-RcXyDzWjBcF7SVGCTSsjsYX?usp=sharing'],
                ['type' => 'link', 'name' => 'General', 'url' => 'https://drive.google.com/drive/folders/1KpN2_KzEflMFlgCqqcFLLnbCVdklu4Zs?usp=sharing'],
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
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => $elemento['type'],
                    'order' => $elementOrder++,
                ], [
                    'content' => [
                        'name' => $elemento['name'],
                        'url' => $elemento['url'],
                    ],
                ]);
            }
        }
    }
}
