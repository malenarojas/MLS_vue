<?php

namespace Database\Seeders;

use App\Models\PriceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PriceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $price_type_list = [
            'Arriendo mensual',
            'Negociable',
            'Ofertas al rededor de',
            'Ofertas sobre',
            'Precio de referencia',
            'Precio Fijo',
            'Precio Solicitado',
            'Precios empiezan en'
        ];

        foreach($price_type_list as $price_type){
            PriceType::create([
                'name' => $price_type
            ]);
        }
    }
}
