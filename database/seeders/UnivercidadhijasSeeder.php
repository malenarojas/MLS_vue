<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Page, Module, Section, Element, AccessRule};
class UnivercidadhijasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            'Clase A' => 1,
            'Clase B' => 1,
        ];

        foreach ($pages as $title => $parentId) {
            // Nivel 1: Clase A o Clase B
            $clasePage = Page::updateOrCreate([
                'title' => $title,
                'module_id' => 1,
                'parent_id' => $parentId,
            ], [
                'image' => '/intranet/Universidad.jpg',
            ]);

            // Nivel 2: Primer Trimestre dentro de cada Clase
            $trimestrePage = Page::updateOrCreate([
                'title' => 'Primer Trimestre',
                'module_id' => 1,
                'parent_id' => $clasePage->id,
            ], [
                'image' => '/intranet/Universidad.jpg',
            ]);

            // 🔹 Secciones especiales en el trimestre
            $seccionesTrimestre = ['Notas', 'Diccionario'];

            foreach ($seccionesTrimestre as $i => $seccionNombre) {
                $section = Section::updateOrCreate([
                    'page_id' => $trimestrePage->id,
                    'title' => $seccionNombre,
                ], [
                    'order' => $i + 1,
                ]);

                // Agregamos elementos demo
                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => 'link',
                    'order' => 1,
                    'content' => [
                        'name' => $seccionNombre . ' General',
                        'url' => 'https://drive.google.com/file/d/1UeZji4vaSap/view',
                    ],
                ]);
            }

            // Nivel 3: Temas dentro del trimestre
            for ($i = 1; $i <= 2; $i++) {
                $temaPage = Page::updateOrCreate([
                    'title' => 'Tema ' . $i,
                    'module_id' => 1,
                    'parent_id' => $trimestrePage->id,
                ], [
                    'image' => '/intranet/Universidad.jpg',
                ]);

                $section = Section::updateOrCreate([
                    'page_id' => $temaPage->id,
                    'title' => 'Contenido del Tema ' . $i,
                ], [
                    'order' => 1,
                ]);

                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => 'link',
                    'order' => 1,
                    'content' => [
                        'name' => 'Video Tema ' . $i,
                        'url' => 'https://drive.google.com/file/d/1UeZji4vaSap/view',
                    ],
                ]);

                Element::updateOrCreate([
                    'section_id' => $section->id,
                    'type' => 'link',
                    'order' => 2,
                    'content' => [
                        'name' => 'Material PDF Tema ' . $i,
                        'url' => 'https://drive.google.com/file/d/1UeZji4vaSap/view',
                    ],
                ]);
            }
        }
    }
}
