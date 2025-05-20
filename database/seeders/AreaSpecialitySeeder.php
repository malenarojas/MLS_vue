<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
class AreaSpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        // Relacionar área COM con especialidades padres
        $comAreaId = DB::table('areas')->where('name', 'COM')->value('id');
        $comSpecialities = DB::table('specialities')
            ->whereIn('name', [
                'Hacienda/Quinta',
                'Industrial',
                'Locales Comerciales',
                'Multifamiliares',
                'Oficinas',
                'Oportunidades de Negocio',
                'Terrenos Comerciales',
            ])
            ->pluck('id');
        


            
        $comSpecialitiesData = $comSpecialities->map(function ($specialityId) use ($comAreaId) {
            return [
                'area_id' => $comAreaId,
                'speciality_id' => $specialityId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
        
        DB::table('area_specialities')->insert($comSpecialitiesData);
        
        // Relacionar área RESCOM con especialidades principales
        $rescomAreaId = DB::table('areas')->where('name', 'RESCOM')->value('id');
        $rescomSpecialities = DB::table('specialities')
            ->whereIn('name', ['Residencial', 'Comercial'])
            ->pluck('id');
        
        $rescomSpecialitiesData = $rescomSpecialities->map(function ($specialityId) use ($rescomAreaId) {
            return [
                'area_id' => $rescomAreaId,
                'speciality_id' => $specialityId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();
        
        DB::table('area_specialities')->insert($rescomSpecialitiesData);



        // Obtener el ID del área COMRES
        $comResAreaId = DB::table('areas')->where('name', 'COMRES')->value('id');

        // Obtener los IDs de las especialidades padres CRResidencial y CRComercial
        $crSpecialities = DB::table('specialities')
            ->whereIn('name', ['CRResidencial', 'CRComercial'])
            ->pluck('id');

        // Crear las relaciones con el área COMRES
        $comResSpecialitiesData = $crSpecialities->map(function ($specialityId) use ($comResAreaId) {
            return [
                'area_id' => $comResAreaId,
                'speciality_id' => $specialityId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        // Insertar las relaciones en la tabla area_specialities
        DB::table('area_specialities')->insert($comResSpecialitiesData);

        
    }
}