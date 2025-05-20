<?php

namespace Database\Seeders;

use App\Constants\NameListingStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los estados usando las constantes
        $StatusListinges = [
            // NameListingStatus::BORRADOR => ['is_final' => false],
            // NameListingStatus::ACTIVA => ['is_final' => false],
            // NameListingStatus::EXPIRADA => ['is_final' => false],
            // NameListingStatus::OFERTA_RESERVA => ['is_final' => false],
            // NameListingStatus::PROPUESTA => ['is_final' => false],
            NameListingStatus::REVIEW => ['is_final' => false],
            NameListingStatus::REJECT => ['is_final' => false],

            // Estados finales
            // NameListingStatus::CANCELADO => ['is_final' => true],
            // NameListingStatus::ALQUILADO => ['is_final' => true],
            // NameListingStatus::VENTA_ACEPTADA_VENDIDA => ['is_final' => true],
        ];

        foreach ($StatusListinges as $name => $attributes) {
            DB::table('status_listings')->insert([
                'name' => $name,
                'is_final' => $attributes['is_final'],
            ]);
        }

        // Obtener todos los estados insertados
        $StatusListingIds = DB::table('status_listings')->pluck('id', 'name')->toArray();

        // Definir las transiciones usando las constantes para los nombres de los estados
        $transitions = [
            // Transiciones de 'Borrador'
            // ['from' => NameListingStatus::BORRADOR, 'to' => NameListingStatus::ACTIVA],
            // ['from' => NameListingStatus::BORRADOR, 'to' => NameListingStatus::EXPIRADA],
            // ['from' => NameListingStatus::BORRADOR, 'to' => NameListingStatus::PROPUESTA],

            // // Transiciones de 'Activa'
            // ['from' => NameListingStatus::ACTIVA, 'to' => NameListingStatus::OFERTA_RESERVA],
            // ['from' => NameListingStatus::ACTIVA, 'to' => NameListingStatus::EXPIRADA],
            // ['from' => NameListingStatus::ACTIVA, 'to' => NameListingStatus::VENTA_ACEPTADA_VENDIDA],
            // ['from' => NameListingStatus::ACTIVA, 'to' => NameListingStatus::CANCELADO],
            // ['from' => NameListingStatus::ACTIVA, 'to' => NameListingStatus::ALQUILADO],

            // // Transiciones de 'Expirada'
            // ['from' => NameListingStatus::EXPIRADA, 'to' => NameListingStatus::ACTIVA],
            // ['from' => NameListingStatus::EXPIRADA, 'to' => NameListingStatus::VENTA_ACEPTADA_VENDIDA],
            // ['from' => NameListingStatus::EXPIRADA, 'to' => NameListingStatus::CANCELADO],

            // // Transiciones de 'Oferta/Reserva'
            // ['from' => NameListingStatus::OFERTA_RESERVA, 'to' => NameListingStatus::VENTA_ACEPTADA_VENDIDA],
            // ['from' => NameListingStatus::OFERTA_RESERVA, 'to' => NameListingStatus::CANCELADO],

            // // Transiciones de 'Propuesta'
            // ['from' => NameListingStatus::PROPUESTA, 'to' => NameListingStatus::ACTIVA],
            // ['from' => NameListingStatus::PROPUESTA, 'to' => NameListingStatus::CANCELADO],

            // Transiciones de 'Revisión'
            ['from' => NameListingStatus::REVIEW, 'to' => NameListingStatus::ACTIVA],
            ['from' => NameListingStatus::REVIEW, 'to' => NameListingStatus::BORRADOR],
            ['from' => NameListingStatus::REVIEW, 'to' => NameListingStatus::REJECT],

            // Transiciones de 'Rechazado'
            ['from' => NameListingStatus::REJECT, 'to' => NameListingStatus::ACTIVA],
            ['from' => NameListingStatus::REJECT, 'to' => NameListingStatus::REVIEW],
        ];

        // Insertar las transiciones
        $transitionData = [];
        foreach ($transitions as $transition) {
            $transitionData[] = [
                'from_status_id' => $StatusListingIds[$transition['from']],
                'to_status_id' => $StatusListingIds[$transition['to']],
            ];
        }

        DB::table('listing_status_transitions')->insert($transitionData);
    }
}
