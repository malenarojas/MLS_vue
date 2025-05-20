<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DocumentationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear el tipo principal para Documentación Pública
         $publicDocumentationId = DB::table('documentation_types')->insertGetId([
            'name' => 'Documentación Pública',
            'parent_id' => null,
        ]);

        // Subtipos para Documentación Pública
        $publicSubtypes = [
            'No seleccionado',
            'Acerca de la ubicación',
            'Avaluo',
            'Certificado Comercial de la Empresa',
            'Certificado Energético',
            'Plano uso de suelo',
            'Certificado libre de gravámenes',
            'Empoderamiento del heredero',
            'Escritura de la casa',
            'CI/Pasaporte',
            'Póliza de seguros',
            'Contrato captacion',
            'Permiso de habitabilidad',
            'Certificado de matrimonio',
            'Aprobación de hipoteca',
            'Oferta',
            'Otro',
            'Documentación de los propietarios',
            'Descripción del Proyecto',
            'Comprobante de residencia',
            'Folleto de propiedad',
            'Mapa de la propiedad',
            'Encuesta de propiedad',
            'Tasa de impuesto a la propiedad',
            'Ficha Técnica de la Propiedad',
            'Título de Propiedad / Escritura',
            'Plano del lugar',
            'Inspección del lugar',
            'Transacción',
            'factura de agua'
        ];

        foreach ($publicSubtypes as $subtype) {
            DB::table('documentation_types')->insert([
                'name' => $subtype,
                'parent_id' => $publicDocumentationId,

            ]);
        }

        // Crear el tipo principal para Documentación Privada
        $privateDocumentationId = DB::table('documentation_types')->insertGetId([
            'name' => 'Documentación Privada',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subtipos para Documentación Privada
        $privateSubtypes = [
            'No seleccionado',
            'Aprobación de Crédito Hipotecario',
            'Avalúo de la Propiedad',
            'Documentación de Captación',
            'Documentos de Cierre',
            'Plano Uso de Suelo'
        ];

        foreach ($privateSubtypes as $subtype) {
            DB::table('documentation_types')->insert([
                'name' => $subtype,
                'parent_id' => $privateDocumentationId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
