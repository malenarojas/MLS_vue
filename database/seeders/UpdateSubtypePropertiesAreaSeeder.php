<?php

namespace Database\Seeders;

use App\Models\SubtypeProperty;
use Illuminate\Database\Seeder;

class UpdateSubtypePropertiesAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $residential = [
            161 => 'Casa',
            162 => 'Apartamento con servicio de hotel',
            163 => 'Apartamento Pareado',
            169 => 'Atípico',
            136 => 'Bloque de apartamentos',
            167 => 'Bodega de vinos',
            168 => 'Cabina',
            42  => 'Casa de Calidad',
            43  => 'Casa de Campo',
            44  => 'Casa de estudiantes',
            45  => 'Casa estilo centro turístico',
            38  => 'Chalet Pareado',
            166 => 'Baulera',
            165 => 'Condominio de lujo',
            132 => 'Desmontable',
            134 => 'Dúplex Pareado',
            135 => 'Dúplex Solitario',
            128 => 'Edificio',
            145 => 'Palacete',
            211 => 'Edificio de apartamentos entero',
            144 => 'Llave en mano',
            143 => 'Espacio de estacionamiento',
            142 => 'Espacio de suelo',
            140 => 'Estudio/Monoambiente',
            98  => 'Finca agrícola',
            217 => 'Garaje permanente',
            146 => 'Monumento-otro',
            147 => 'uno a uno',
            131 => 'Departamento',
            133 => 'Dúplex',
            47  => 'Casa con Espacio Comercial',
            48  => 'Casa en esquina',
            190 => 'Penthouse',
            27  => 'Quinta',
            101 => 'Terreno',
            155 => 'Apartamento vacacional',
        ];

        $commercial = [
            83  => 'Club',
            4   => 'Cafetería',
            12  => 'Gasolinera',
            87  => 'Gimnasio',
            77  => 'Hotel/Edificio de apartamentos',
            79  => 'Alojamiento',
            88  => 'Centro deportivo',
            89  => 'Cine',
            72  => 'Clínica de salud',
            114 => 'Edificio/Construcción',
            54  => 'Espacio artesanal',
            55  => 'Galpon',
            210 => 'Garaje/Baulera',
            76  => 'Hotel boutique',
            8   => 'Local Comercial',
            6   => 'Negocio',
            62  => 'Oficina',
            63  => 'Oficina Virtual',
            10  => 'Para llevar / Quiosco',
            124 => 'Piso del edificio',
            91  => 'Plaza',
            90  => 'Teatro',
            110 => 'TERRENO COMERCIAL',
            108 => 'Terreno de juego',
            7   => 'Venta de Negocio',
            127 => 'vestíbulo',
            25  => 'Villa',
            96  => 'Propiedad Agrícola/Ganadera',
            212 => 'Otros',
        ];

        // Primero, resetear todos a null
        SubtypeProperty::query()->update([
            'area_id' => null,
        ]);

        // Actualizar residenciales
        foreach ($residential as $id => $name) {
            SubtypeProperty::where('id', $id)->update([
                'name' => $name,
                'area_id' => 2,
            ]);
        }

        // Actualizar comerciales
        foreach ($commercial as $id => $name) {
            SubtypeProperty::where('id', $id)->update([
                'name' => $name,
                'area_id' => 1,
            ]);
        }
    }
}
