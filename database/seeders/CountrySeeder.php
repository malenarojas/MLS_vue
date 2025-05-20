<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$countries = [
            ['name' => 'Peru', 'code' => 'AF', 'phone_code' => '+93', 'flag_svg' => 'https://flagcdn.com/w40/pe.png'],
            ['name' => 'Albania', 'code' => 'AL', 'phone_code' => '+355', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#e41e20" d="M0 0h640v480H0z"/><path d="M320 240l-50 50 10-50-50-10 50-10-10-50 50 50 50-50-10 50 50 10-50 10 10 50z" fill="#000"/></svg>'],
            ['name' => 'Argentina', 'code' => 'AR', 'phone_code' => '+54', 'flag_svg' => 'https://flagcdn.com/w40/ar.png'],
            ['name' => 'Bolivia', 'code' => 'BO', 'phone_code' => '+591', 'flag_svg' => 'https://flagcdn.com/w40/bo.png'],
            ['name' => 'Brazil', 'code' => 'BR', 'phone_code' => '+55', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#009739" d="M0 0h640v480H0z"/><path fill="#fdd216" d="M320 100l200 140-200 140-200-140z"/><circle cx="320" cy="240" r="70" fill="#002776"/></svg>'],
            ['name' => 'Canada', 'code' => 'CA', 'phone_code' => '+1', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#fff" d="M0 0h640v480H0z"/><path fill="#d52b1e" d="M0 0h213.3v480H0z"/><path fill="#d52b1e" d="M426.7 0H640v480H426.7z"/><path d="M320 120l30 100-80-60h100l-80 60z" fill="#d52b1e"/></svg>'],
            ['name' => 'Chile', 'code' => 'CL', 'phone_code' => '+56', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><path fill="#fff" d="M0 0h640v320H0z"/><path fill="#d52b1e" d="M0 320h640v160H0z"/><path fill="#0033a0" d="M0 0h213.3v160H0z"/><circle cx="106.6" cy="80" r="40" fill="#fff"/></svg>'],
            ['name' => 'Colombia', 'code' => 'CO', 'phone_code' => '+57', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480"><g fill-rule="evenodd"><path fill="#fcd116" d="M0 0h640v240H0z"/><path fill="#0033a0" d="M0 240h640v120H0z"/><path fill="#ce1126" d="M0 360h640v120H0z"/></g></svg>'],
            ['name' => 'United States', 'code' => 'US', 'phone_code' => '+1', 'flag_svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1235 650"><path fill="#bd3d44" d="M0 0h1235v650H0z"/><path fill="#fff" d="M0 50h1235m0 100H0m0 100h1235m0 100H0m0 100h1235m0 100H0" stroke="#fff" stroke-width="50"/><path fill="#192f5d" d="M0 0h494v350H0z"/></svg>'],
        ];

        DB::table('countries')->insert($countries);
    }
}
