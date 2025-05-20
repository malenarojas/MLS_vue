<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $languages = [
        //     'africaans', 'Albanés', 'Alemán', 'Antingala', 'Árabe', 'arameo', 'Armenio',
        //     'Azerbaijani', 'Bahasa Melayu', 'bosnio', 'Búlgaro', 'Cantonés', 'Catalán',
        //     'Checo', 'Cingalés', 'Croata', 'Danés', 'Eslovaco', 'Esloveno', 'Español',
        //     'Estonio', 'Filipino', 'Finlandés', 'Francés', 'Francés - Canada', 'Gaélico',
        //     'Galés', 'Georgian', 'Griego', 'Gujarati', 'Hebreo', 'Hindi', 'Holandés',
        //     'Húngaro', 'Indú', 'Inglés', 'Islándico', 'Italiano', 'Japonés', 'Kanarese',
        //     'Kannada', 'Koreano', 'Kurdish', 'Lenguaje de Signos', 'Letón', 'Lituano',
        //     'Luganda', 'Luxemburgués', 'Macedonio', 'Malay', 'Malayalam', 'Maltés', 'Mandarín',
        //     'Marathi', 'Mongolian', 'Montenegrino', 'Noruego', 'Oriya', 'Panyabí', 'Pashto',
        //     'Persa', 'Persa Farsí', 'Polaco', 'Portugués', 'Romansh', 'Rumano', 'Ruso',
        //     'Serbio', 'Sesotho', 'Shanghai', 'Sindhi', 'Suazi', 'Sueco', 'Swahili', 'Tailandés',
        //     'Taiwanés', 'Tamil', 'Telugu', 'Tibetano', 'Turco', 'Ucraniano', 'Urdu',
        //     'Valencian', 'Vietnamita', 'Xhosa', 'Yídish', 'Yugoslavo', 'Zulú'
        // ];

        $languages = [
            [
                'name' => 'Español',
                'code' => 'ESB',
                'is_default' => 1,
                'country_code' => '+591',
                'country_coordinate' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><g fill-rule="evenodd"><path fill="#007847" d="M0 0h640v480H0z"/><path fill="#FFD700" d="M0 0h640v320H0z"/><path fill="#DA121A" d="M0 0h640v160H0z"/></g></svg>', // Bandera de Bolivia
            ],
            [
                'name' => 'English',
                'code' => 'ENB',
                'is_default' => 0,
                'country_code' => '+44',
                'country_coordinate' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#00247d" d="M0 0h640v480H0z"/><path fill="#fff" d="M77 0v480M0 160h640M640 320H0M563 480V0"/><path fill="#cf142b" d="M0 192h640v96H0z"/><path d="M240 0v480M400 0v480M0 128h640M0 352h640" fill="#cf142b"/></svg>', // Bandera del Reino Unido
            ],
            [
                'name' => 'Portugués',
                'code' => 'PTO',
                'is_default' => 0,
                'country_code' => '+351',
                'country_coordinate' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#060" d="M0 0h640v480H0z"/><path fill="#ffcc29" d="M0 0h160v480H0z"/><circle cx="80" cy="240" r="48" fill="#fff"/><circle cx="80" cy="240" r="40" fill="#e62a00"/></svg>', // Bandera de Portugal
            ],
        ];
        foreach ($languages as $language) {
            DB::table('languages')->insert($language);
        }
    }
}
