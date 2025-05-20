<?php

namespace Database\Seeders;

use App\Models\CancellationReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CancellationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'Agent Deactivation',
            'Alquilado / vendido en otro ID de MLS',
            'Listado erróneo',
            'Marketing Pobre',
            'No puede contactarse con cliente',
            'Precio Inadecuado',
            'Promoción de marketing',
            'Vendido por Otro Agente',
            'Disputa con cliente',
            'Propiedad duplicada',
            'Cambio de Agente RE/MAX',
            'Cliente no sigue vendiendo',
            'Demasiado tiempo de espera',
            'Dificultades Financieras',
            'Disputa con el agente',
            'Insuficientes visitas',
            'Perdieron con la competencia',
            'Vendido por el Propietario',
            'Venta Privada',
        ];

        foreach ($reasons as $reason) {
            CancellationReason::firstOrCreate(['name' => $reason]);
        }
    }
}
