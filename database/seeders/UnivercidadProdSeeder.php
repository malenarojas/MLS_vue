<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\{Page, Module, Section, Element, AccessRule};

class UnivercidadProdSeeder extends Seeder
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


        $moduleuni = Module::where('name', 'Universidad RE/MAX')->firstOrFail();

        // Obtenemos la página padre (Universidad RE/MAX)
        $parentPage = Page::where('title', 'Universidad RE/MAX')
                         ->where('module_id', $moduleuni->id)
                         ->firstOrFail();

        // Creamos la página hija para Webinarios LATAM
        $pagecapacitacion = Page::updateOrCreate([
            'title' => 'Capacitación Gerencial',
            'module_id' => $moduleuni->id,
            'parent_id' => $parentPage->id,
        ], [
            'image' => '/intranet/Universidad.jpg',
        ]);

        // Creamos la página hija para Webinarios LATAM
        $pagewebinarios = Page::updateOrCreate([
            'title' => 'Webinarios RE/MAX LATAM',
            'module_id' => $moduleuni->id,
            'parent_id' => $parentPage->id,
        ], [
            'image' => '/intranet/Universidad.jpg',
        ]);

        // Definimos las secciones y elementos basados en el HTML proporcionado
        $seccioneswebinarios = [
            'RE/MAX LATAM 2024' => [
                ['type' => 'link', 'name' => 'Tiempo inteligente: Curso de productividad personal', 'url' => 'https://remax.zoom.us/rec/share/B5BXJqCatxfWj2dBeVhX_8x289kvmjldISBkiJ1TZoHig8ks9f8eLrnS0bxLtYo.e-p3HP9ENizFuuNK?startTime=1713364530000'],
            ],
            'RE/MAX LATAM 2021' => [
                ['type' => 'link', 'name' => 'Tecnologia Humana - Francisco Paillie', 'url' => 'https://drive.google.com/file/d/1RPHfaPzl_66Ad7oK7gSdtF3UIMfSDRjd/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Brokers R&R Fernando Ascani', 'url' => 'https://drive.google.com/file/d/1qbJkkEJxSMusYOUyvA18JAR1X_ZK5BK5/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Precio para que se Venda - Klever Guanoluisa', 'url' => 'https://drive.google.com/file/d/1dO0RkNrIZ79PbuYlRlT6UrpUIQGuM66q/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Marketing y Redes Sociales para el Exito - Alejandra Kallsen', 'url' => 'https://drive.google.com/file/d/1kmO4HTK7FV4BxUOMA8yQc8-3l4Sf-wuO/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Brokers Daniela Jara & Rafaela Urrutia', 'url' => 'https://drive.google.com/file/d/1YgcZ0jHjeUp-MrjiNPP6AgnflMaCdUSr/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Oratoria y Comunicacion - Hugo Viruez', 'url' => 'https://drive.google.com/file/d/1tJnYph9kTOSXq-q-4sZWg5eW-o2rb5jK/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Montaña Rusa - Yuval Ben Haym', 'url' => 'https://drive.google.com/file/d/18tEuEbO5PVhTb10Qj3EJjdwIX0k4Ei0l/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Liderazgo Moderno', 'url' => 'https://drive.google.com/file/d/18VhfgBt5dRAwVn3a5Fumy_XPG8efqfZG/view?usp=sharing'],
            ],
            'RE/MAX LATAM 2020' => [
                ['type' => 'link', 'name' => 'Los bienes raices Comerciales 2021 - Cesar Lopez', 'url' => 'https://drive.google.com/file/d/1ych2zy0E59hRu9hfcTtCYXdR8sctwua-/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Reclutamento 10x - Karin Oviedo', 'url' => 'https://drive.google.com/file/d/1_BM30_0evOThpuRCbW5BaKG5im6nKfWP/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Prospeccion Llamadas Exitosas', 'url' => 'https://drive.google.com/file/d/1DqneN4FZwx1HB9U6jvzvyHlyuBES2jG4/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Panel de Brokers', 'url' => 'https://drive.google.com/file/d/1gyQFWNMFPyr-jm6pX2oU1NQL1jQPn4sL/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Cliente Comprador y Vendedor - Maximiliano Bause', 'url' => 'https://drive.google.com/file/d/1mcqgkw1lARzKMjmoWU_G2Mw11R4LVkY4/view?usp=sharing'],
            ],
        ];

        $order = 1;
        foreach ($seccioneswebinarios as $title => $elementos) {
            $section = Section::updateOrCreate(
                ['page_id' => $pagewebinarios->id, 'title' => $title],
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

        // Actualizamos la sección de Videos Capacitacion RE/MAX LATAM en la página padre
       /* $parentSection = Section::where('page_id', $parentPage->id)
                               ->where('title', 'Videos Capacitacion RE/MAX LATAM')
                               ->first();

        if ($parentSection) {
            // Actualizamos el elemento existente para que apunte a esta nueva página
            Element::updateOrCreate([
                'section_id' => $parentSection->id,
                'type' => 'page',
            ], [
                'order' => 1,
                'content' => [
                    'name' => 'Webinarios RE/MAX LATAM',
                    'page_id' => $page->id,
                ],
            ]);
        }*/

        $secciones = [
            'RE/MAX NEWS' => [
                ['type' => 'link', 'name' => 'RE/MAX NEWS 11 DE MAYO 2022', 'url' => 'https://drive.google.com/file/d/1UZHmnz8lq-XxWEhA5jHxEvvrXjgVChN3/view?usp=sharing'],
            ],
            'CAPACITACIONES TEMATICAS' => [
                ['type' => 'link', 'name' => 'LAS CLAVES PARA HACER UNA OFICINA REMAX RENTABLE.mp4', 'url' => 'https://drive.google.com/file/d/1EV3l1b5nVEIBFSbu4vK5ZhmqQWKjs0Dk/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'CONSTRUCCION DE RELACIONES CON CLIENTES.mp4', 'url' => 'https://drive.google.com/file/d/126CGLNpEYb3V1FUMoHtKZl7AebwJzgv8/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'PLANIFICACION DE TU EMPRENDIMIENTO INMOBILIARIO.mp4', 'url' => 'https://drive.google.com/file/d/1S6YQjHSry9QKfFg60k02tdev6x5daYAh/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'PUBLICIDAD PAGA EN META.mp4', 'url' => 'https://drive.google.com/file/d/1CTIi3RtXVVcS14Y0hw9u76HM029T764c/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'OPTIMIZANDO LLAMADAS EN FRIO PARA AGENTES INMOBILIARIOS - 9 DE OCTUBRE 2024', 'url' => 'https://drive.google.com/file/d/1H92E7aQgdgpBGGtc7gX0CS3uiCvn5k_3/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'CAPACITACION IFRANCHISE', 'url' => 'https://drive.google.com/file/d/1uO84FxpL9f_-2Fc75sp6ah4Abidp_dMx/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Taller de Captaciones', 'url' => 'https://drive.google.com/file/d/1VP127RZkDs7oTLszDZ6lrnYGTBPMwI0q/view?usp=sharing'],
                ['type' => 'link', 'name' => 'CAPACITACIÓN MARKETING & PHOTOFY', 'url' => 'https://drive.google.com/file/d/1XYX_81SJFrsIryhlXW-E4kW9MHCKaJQa/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Power What\'s Possible', 'url' => 'https://drive.google.com/file/d/1Z4pbaTA8dGsKytHJ7hBHNEmbm-SaVgyw/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Potenciando tus Prospectos en ILIST', 'url' => 'https://drive.google.com/file/d/18XSjvHAu1sJosBRH9vlrk6BgHa-hlMo2/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RE/POTENCIANDO TU NEGOCIO 3', 'url' => 'https://drive.google.com/file/d/1yCfymyDYDSeh3emVZIQqOqraCEhgolLi/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RE/POTENCIANDO TU NEGOCIO 2', 'url' => 'https://drive.google.com/file/d/1UhgOV-NWjW1aOKePNETPcjhxO16fr_Ng/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RE/POTENCIANDO TU NEGOCIO 1', 'url' => 'https://drive.google.com/file/d/153P7P0Oqu14rGevesK0rW739v4l0Ofs8/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Manejo De Marca RE/MAX Bolivia', 'url' => 'https://drive.google.com/file/d/17Eku0ypip3e--C8CplZVyWjuAOU4-Ouc/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RECLUTAMIENTO LA FUNCIÓN PRINCIPAL DEL BROKER parte 1', 'url' => 'https://drive.google.com/file/d/1kpWJ0S46h2qqNS6OaSbMqNXxgWepqOQ-/view?usp=sharing'],
                ['type' => 'link', 'name' => 'RECLUTAMIENTO LA FUNCIÓN PRINCIPAL DEL BROKER parte 2', 'url' => 'https://drive.google.com/file/d/1X_WjGSQAYQzcQuQ1FNpbqWqC8iATkLE7/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Contabilidad Inmobiliaria', 'url' => 'https://drive.google.com/file/d/199h_x7mgD0b0Nsnqb8Sx7GxX8I0IF6Vw/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Analisis Comparativo de Mercado KNOW HOW', 'url' => 'https://drive.google.com/file/d/1KDmctNt9OzSpKCMzLcJvlFLXSFDAE-1b/view?usp=sharing'],
                ['type' => 'link', 'name' => 'ABC Legal', 'url' => 'https://drive.google.com/file/d/10XZkAoJv0DHSfePNAzpMkwxG_BA7Fx3v/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Agentes Top 100', 'url' => 'https://drive.google.com/file/d/10yUaEqsO8JOuLbAnQXwHd3YiWJmTOJMX/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Especialista en Bienes Raíces Residenciales', 'url' => 'https://drive.google.com/file/d/1iKz02x2bt8fLwwmH9J2SWsd4SbYZOCvo/view?usp=sharing'],
            ],
            'Video' => [
                ['type' => 'link', 'name' => 'Video university', 'url' => '/Sites/remaxbolivia/intranet/universidad/Video%20university.mp4'],
            ],
            'Archivos PDF' => [
                ['type' => 'link', 'name' => 'FASE INICIAL-REMAX BOLIVIA HVM 2.0  2021.pdf', 'url' => '/Sites/remaxbolivia/intranet/universidad/FASE%20INICIAL-REMAX%20BOLIVIA%20HVM%202.0%20%202021.pdf'],
                ['type' => 'link', 'name' => 'FASE INTERMEDIA-REMAX BOLIVIA HVM 2.0  2021.pdf', 'url' => '/Sites/remaxbolivia/intranet/universidad/FASE%20INTERMEDIA-REMAX%20BOLIVIA%20HVM%202.0%20%202021.pdf'],
                ['type' => 'link', 'name' => 'FASE AVANZADA -REMAX BOLIVIA HVM  2.0 2021.pdf', 'url' => '/Sites/remaxbolivia/intranet/universidad/FASE%20AVANZADA%20-REMAX%20BOLIVIA%20HVM%20%202.0%202021.pdf'],
                ['type' => 'link', 'name' => 'MIS PRIMEROS 100 DÍAS CON REMAX (AGENTE ASOCIADO).pdf', 'url' => '/Sites/remaxbolivia/intranet/universidad/MIS%20PRIMEROS%20100%20D%C3%8DAS%20CON%20REMAX%20(AGENTE%20ASOCIADO).pdf'],
            ],
            'Videos Capacitacion Gerencial' => [
                ['type' => 'page', 'name' => 'Capacitación Gerencial', 'page_id' => $pagecapacitacion->id ],
            ],
            'Videos Capacitacion RE/MAX LATAM' => [
                ['type' => 'page', 'name' => 'Webinarios RE/MAX LATAM', 'page_id' => $pagewebinarios->id],
            ],
            'Videos Fase Inicial' => [
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 1', 'url' => 'https://drive.google.com/file/d/1qt8CVB4tc9UBeKzgeDZvVzMoV_zvb5iI/view?usp=sharing'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 2', 'url' => 'https://drive.google.com/file/d/1Zkeq3lMPhXl3hsZh8_6nJ27O-OJFXAyq/view?usp=sharing'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 3', 'url' => 'https://drive.google.com/file/d/1XbaouPwfED-sxhuAf28qaNH38Xsv8yIK/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'FASE INICIAL Clase 3 prueba', 'url' => 'https://drive.google.com/file/d/1gcb2BJhuunqczoiirGFvWX_SGJgo-tAD/view?usp=drive_link'],
                ['type' => 'link', 'name' => 'Fase Inicial Sistemas', 'url' => 'https://drive.google.com/file/d/1XbaouPwfED-sxhuAf28qaNH38Xsv8yIK/view?usp=sharing'],
            ],
            'Videos Fase Intermedia' => [
                ['type' => 'link', 'name' => 'Fase Intermedia Clase 1', 'url' => 'https://drive.google.com/file/d/1PX7XNyxr0Rdcb_EGigMt-5qzB7KM_DRZ/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Fase Intermedia Clase 2', 'url' => 'https://drive.google.com/file/d/1bQNwgXtUjcSZK1KVuOh-8tPJjLqzvX3I/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Fase Intermedia Sistemas', 'url' => 'https://drive.google.com/file/d/1acQAPe8nTSXhPQQ51pgRmGQ4ejtlvnLZ/view?usp=sharing'],
            ],
            'Videos Fase Avanzada' => [
                ['type' => 'link', 'name' => 'Fase Avanzada Clase 1', 'url' => 'https://drive.google.com/file/d/1XIfwHC9MBVKoQVE_MqF0cHbuqnD5GtqX/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Fase Avanzada Clase 2', 'url' => 'https://drive.google.com/file/d/1pBRoI38YW_ccavyLkGfI-dt54McQ-L0q/view?usp=sharing'],
                ['type' => 'link', 'name' => 'Fase Avanzada Sistemas', 'url' => 'https://drive.google.com/file/d/1KyfiPi8w4gex4UOY_1QuRfrhJkjFpMe_/view?usp=sharing'],
            ],
            '60 Minutos Agentes' => [
                ['type' => 'link', 'name' => '60 Minutos Agentes', 'url' => 'https://drive.google.com/file/d/1tW33CGGaS0xWYMqKqvlAHKW6HBYgHMoX/view?usp=sharing'],
                ['type' => 'link', 'name' => '60 Minutos Agentes.PPT', 'url' => 'https://docs.google.com/presentation/d/1oL350k4di0HPegFDRj7jx1RMWAF2Wa1M/edit?usp=sharing&ouid=107594293886754996859&rtpof=true&sd=true'],
            ],
            'Formularios' => [
                ['type' => 'link', 'name' => 'RB 02 Formulario de Registro de Nuevos Miembros', 'url' => '/Sites/remaxbolivia/intranet/Documents/RB%2002%20Formulario%20de%20Registro%20de%20Nuevos%20Miembros.pdf'],
                ['type' => 'link', 'name' => 'RB.RU - 01 - Fases inicial, intermedia y avanzada', 'url' => '/Sites/remaxbolivia/intranet/Documents/RB.RU%20-%2001%20-%20Fases%20inicial,%20intermedia%20y%20avanzada.pdf'],
                ['type' => 'link', 'name' => 'RB.RU - 02 - CIRB (Mod.1,2 y3)', 'url' => '/Sites/remaxbolivia/intranet/Documents/RB.RU%20-%2002%20-%20CIRB%20(Mod.1,2%20y3).pdf'],
                ['type' => 'link', 'name' => 'RB 01 Formulario de Cambio de estado vA01', 'url' => '/Sites/remaxbolivia/intranet/Documents/RB%2001%20Formulario%20de%20Cambio%20de%20estado%20vA01.pdf'],
            ],
            'Glossario' => [
                ['type' => 'link', 'name' => '2019 ReMax Glossary DIGITAL', 'url' => '/Sites/remaxbolivia/intranet/Documents/2019-ReMax-Glossary-DIGITAL.pdf'],
            ],
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
                    'content' => $item['type'] === 'page'
                    ? [
                        'name' => $item['name'],
                        'page_id' => $item['page_id'],
                    ]
                    : [
                        'name' => $item['name'],
                        'url' => $item['url'],
                    ],
                ]);
            }
        }


    }
}
