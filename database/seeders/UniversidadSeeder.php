<?php

namespace Database\Seeders;

use App\Models\{Page, Module, Section, Element, AccessRule};
use Illuminate\Database\Seeder;

class UniversidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module = Module::updateOrCreate([
            'name' => 'Universidad RE/MAX'
        ], [
            'image' => '/intranet/Universidad.jpg'
        ]);

        $page = Page::updateOrCreate([
            'title' => 'Universidad RE/MAX',
            'module_id' => $module->id,
        ], [
            'image' => '/intranet/Universidad.jpg',
        ]);

        $module->update(['default_page_id' => $page->id]);

        $sections = [
            'RE/MAX NEWS' => [
                ['type' => 'link', 'name' => 'RE/MAX NEWS 11 DE MAYO 2022', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'CAPACITACIONES TEMÁTICAS' => [
                ['type' => 'link', 'name' => 'LAS CLAVES PARA HACER UNA OFICINA REMAX RENTABLE.mp4', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'CONSTRUCCIÓN CON RELACIONES CON CLIENTES.mp4', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'PLANIFICACIÓN DE TU EMPRENDIMIENTO INMOBILIARIO.mp4', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'PUBLICIDAD PAGA EN METAM.P4', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'OPTIMIZANDO LLAMADAS EN FRÍO PARA AGENTES INMOBILIARIOS - 9 DE OCTUBRE 2024', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'CAPACITACIÓN FRANCHISE', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Taller de Capacitaciones', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'CAPACITACIÓN MARKETING & PHOTOFY', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => "Power What's Possible", 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Potenciando tus Prospectos en ILIST', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RE/POTENCIANDO TU NEGOCIO 1', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RE/POTENCIANDO TU NEGOCIO 2', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Manejo De Marca RE/MAX Bolivia', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RECLUTAMIENTO LA FUNCIÓN PRINCIPAL DEL BROKER parte 1', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RECLUTAMIENTO LA FUNCIÓN PRINCIPAL DEL BROKER parte 2', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Contabilidad Inmobiliaria', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Análisis Comparativo de Mercado KNOW HOW', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'ABC Legal', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Agentes Top 100', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Especialista en Bienes Raíces Residenciales', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Archivos PDF' => [
                ['type' => 'link', 'name' => 'FASE INICIAL-REMAX BOLIVIA HVM 2.0 2021.pdf', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'FASE INTERMEDIA-REMAX BOLIVIA HVM 2.0 2021.pdf', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'FASE AVANZADA -REMAX BOLIVIA HVM 2.0 2021.pdf', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'MIS PRIMEROS 100 DÍAS CON REMAX (AGENTE ASOCIADO).pdf', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Videos Capacitación Gerencial' => [
                ['type' => 'link', 'name' => 'Capacitación Gerencial', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Videos Capacitación RE/MAX LATAM' => [
                ['type' => 'link', 'name' => 'Webinarios RE/MAX LATAM', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Videos Fase Inicial' => [
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 1', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 2', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 3', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 3 prueba', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Fase Inicial Sistemas', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Videos Fase Intermedia' => [
                ['type' => 'link', 'name' => 'Fase Intermedia Clase 1', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Fase Intermedia Clase 2', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Fase Intermedia Sistemas', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Videos Fase Avanzada' => [
                ['type' => 'link', 'name' => 'Fase Avanzada Clase 1', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Fase Avanzada Clase 2', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'Fase Avanzada Sistemas', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            '60 Minutos Agentes' => [
                ['type' => 'link', 'name' => '60 Minutos Agentes', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => '60 Minutos Agentes.PPT', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Formularios' => [
                ['type' => 'link', 'name' => 'RB 02 Formulario de Registro de Nuevos Miembros', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RB.RU - 01 - Fases inicial, intermedia y avanzada', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RB.RU - 02 - CIR8 (Mod.1,2 y3)', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
                ['type' => 'link', 'name' => 'RB 01 Formulario de Cambio de estado vA01', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            'Glosario' => [
                ['type' => 'link', 'name' => '2019 ReMax Glossary DIGITAL', 'url' => 'https://drive.google.com/file/d/1UeZji4vaSap_FY6zZ1FhwXtfn5oaXIaH/view'],
            ],
            
        ];

        $order = 1;

        foreach ($sections as $sectionTitle => $links) {
            $section = Section::updateOrCreate([
                'page_id' => $page->id,
                'title' => $sectionTitle,
            ], [
                'order' => $order++,
            ]);

            $elementOrder = 1;

            foreach ($links as $link) {
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => 'link',
                    'order' => $elementOrder++,
                    'content' => [
                        'name' => $link['name'],
                        'url' => $link['url'], // Agrega los enlaces reales aquí si los tienes
                    ],
                ]);
            }
        }

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
    }
}
