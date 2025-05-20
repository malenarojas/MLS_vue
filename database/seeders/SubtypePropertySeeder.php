<?php

namespace Database\Seeders;

use App\Models\SubtypeProperty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubtypePropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subtypes = [
            'Comercial' => [
                'Comercial/Negocio',
                'Restaurante',
                'Bar',
                'Cafetería',
                'Disco',
                'Negocio',
                'Venta de Negocio',
                'Local Comercial',
                'Para llevar',
                'Para llevar / Quiosco',
                'Autorrestaurante',
                'Gasolinera'
            ],
            'Educativo' => [
                'Colegio',
                'Universidad',
                'Campus Alojamiento'
            ],
            'Residencial' => [
                'Apartamento de servicio',
                'Casa de huéspedes',
                'Casa Impuesto',
                'Casa Pareada',
                'Casa Adosada',
                'Casa tipo Townhouse',
                'Casa para dos Familias',
                'Apartamento para la Tercera Edad',
                'Triplex o superior',
                'Villa',
                'Chalet',
                'Quinta',
                'Casa Pequeña',
                'Casa en el Agua (flotante)',
                'Casa con dos niveles',
                'Bungalow conectado',
                'Casa con comercio interno',
                'Casa 2 Niveles',
                'Chalet Independiente Bung',
                'Casa Residencial Sin adosar',
                'Casa en el Dique',
                'Casa de Caballeros',
                'Chalet Pareado',
                'Casa Cuadrante',
                'Casa Semi-Adosada',
                'Semi-Detached Chalet Bungalow',
                'Casa de Calidad',
                'Casa de Campo',
                'Casa de estudiantes',
                'Casa estilo centro turístico',
                'Casa Patrimonio Arquitectónico',
                'Casa con Espacio Comercial',
                'Casa en esquina',
                'Subsuelo',
                'Isla privada'
            ],
            'Industrial' => [
                'Industrial',
                'Fábrica',
                'Edificio Area Industrial',
                'Espacio artesanal',
                'Galpon',
                'Almacén con Apto/Oficina',
                'Almacén único Inquilino',
                'Almacén-Multi Inquilino',
                'Almacenamiento',
                'Ingenio',
                'Molino'
            ],
            'Oficinas' => [
                'Oficina',
                'Oficina Virtual',
                'Oficina venta al detalle',
                'Oficina administrada',
                'Espacio de oficina'
            ],
            'Institucional y Salud' => [
                'Vida congregada',
                'Asilo de ancianos',
                'Rehabilitación',
                'Hospital',
                'Cuidado ambulatorio',
                'Clínica de salud'
            ],
            'Hospitalidad y Alojamiento' => [
                'Hotel',
                'Hotel Resort Internacional',
                'Hotel pequeño',
                'Hotel boutique',
                'Hotel/Edificio de apartamentos',
                'Posada',
                'Alojamiento',
                'B&B',
                'Casa de huéspedes'
            ],
            'Recreacional' => [
                'Centro de entrenamiento',
                'Club',
                'Parque navideño',
                'Parque de visitas',
                'Parque para Campamento',
                'Gimnasio',
                'Centro deportivo',
                'Cine',
                'Teatro',
                'Plaza',
                'Parque de casas móviles'
            ],
            'Agrícola / Ganadero' => [
                'Hacienda',
                'Granja de granos',
                'Granja de ganado',
                'Propiedad Agrícola/Ganadera',
                'Granja para Agricultura',
                'Finca agrícola'
            ],
            'Inversión y Desarrollo' => [
                'Inversión',
                'Sitio de construcción',
                'Terreno',
                'Para construcción',
                'Terreno Baldío',
                'Plano de terreno expectativa de construcción',
                'Plano de tierra para Agricultura',
                'Plano de terreno para Servicios',
                'Plano de terreno para construcción',
                'Terreno de juego',
                'Parque de casas móviles',
                'TERRENO COMERCIAL',
                'Terreno para casas rodantes',
                'Terreno para Construcción futura',
                'Terreno para Desarrollo'
            ],
            'Otros' => [
                'Edificio/Construcción',
                'Operacional',
                'Inquilino único',
                'Torre de altura mediana',
                'Centro de tiras',
                'Centro comercial',
                'Centro comercial de venta',
                'De pie',
                'Cuarto de Exposición (Show Room)',
                'Comprar inquilino soltero',
                'Piso del edificio',
                'Regional encerrado',
                'Regional no encerrado',
                'vestíbulo',
                'Edificio',
                'Edificio alto',
                'Desván',
                'Departamento',
                'Desmontable',
                'Dúplex',
                'Dúplex Pareado',
                'Dúplex Solitario',
                'Bloque de apartamentos',
                'Camarote',
                'Cooperativa',
                'Establo',
                'Estudio/Monoambiente',
                'Espacio artesanal',
                'Espacio de suelo',
                'Espacio de estacionamiento',
                'Llave en mano',
                'Palacete',
                'Monumento-otro',
                'uno a uno',
                'Madera',
                'Propiedad para rendimiento',
                'Granero para Conversión',
                'Terrenos para construcción',
                'Parcela',
                'Bodega',
                'Estacionamiento',
                'Apartamento Vacacional',
                'Triple',
                'Apartamento del ático',
                'Cuarto para estudiante',
                'Habitaciones'
            ],
        ];

        foreach ($subtypes as $typeName => $subtypeNames) {
            $typeId = DB::table('type_properties')->where('name', $typeName)->value('id');
            foreach ($subtypeNames as $subtypeName) {
                DB::table('subtype_properties')->insert([
                    'name' => $subtypeName,
                    'type_property_id' => $typeId,
                ]);
            }
        }

        /**
         *      NO ELIMINAR NI ACOMODAR !!!
         * Importante para el script de migracion
         */

        DB::table('type_properties')->insert([
            'name' => 'Garage/Baulera'
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Garage',
            'type_property_id' => 12,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Casa',
            'type_property_id' => 3,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Apartamento con servicio de hotel',
            'type_property_id' => 7,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Apartamento pareado',
            'type_property_id' => 7,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Garage Permanente',
            'type_property_id' => 12,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Condominio de Lujo',
            'type_property_id' => 3,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Baulera',
            'type_property_id' => 11,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Bodega de vinos',
            'type_property_id' => 11,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Cabina',
            'type_property_id' => 11,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'Atípico',
            'type_property_id' => 11,
        ]);

        DB::table('subtype_properties')->insert([
            'name' => 'VK.com',
            'type_property_id' => 11,
        ]);
    }
}
