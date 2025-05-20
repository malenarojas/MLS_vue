<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Especialidades principales (padres) estas relacionadas con el Area ResCom
        $residencialId = DB::table('specialities')->insertGetId([
            'name' => 'Residencial',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $comercialId = DB::table('specialities')->insertGetId([
            'name' => 'Comercial',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subespecialidades de Residencial
        DB::table('specialities')->insert([
            ['name' => 'Comunidades mayores', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oportunidades de negocio', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ventas cortas', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administración de Bienes', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casas de Lujo', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Propiedades Vacacionales', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Comercial
        DB::table('specialities')->insert([
            ['name' => 'Hacienda/Quinta', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Industrial', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Locales Comerciales', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Multifamiliares', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oficinas', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Terrenos Comerciales', 'parent_id' => $comercialId, 'created_at' => now(), 'updated_at' => now()],
        ]);


          // Crear las especialidades principales (padres)
          $CRresidencialId = DB::table('specialities')->insertGetId([
            'name' => 'CRResidencial',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $CRcomercialId = DB::table('specialities')->insertGetId([
            'name' => 'CRComercial',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subespecialidades de CRResidencial
        DB::table('specialities')->insert([
            ['name' => 'Comunidades mayores', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oportunidades de negocio', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ventas cortas', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ventas fuertes', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administración de Bienes', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alquiler', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casas de Lujo', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Compradores', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Condominios', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Construcciones Nuevas', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desarrollo de Terrenos', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Haciendas y Terrenos', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Internacional (transacciones en el extranjero)', 'parent_id' => $residencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Inversión', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Militar', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Propiedad con Caballos', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Propiedades al Lago', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Propiedades embargadas', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Propiedades Vacacionales', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Quintas Residenciales', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Remates', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reubicaciones', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tiempo Compartido', 'parent_id' => $CRresidencialId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de CRComercial
        DB::table('specialities')->insert([
            ['name' => 'Hacienda/Quinta', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Industrial', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Locales Comerciales', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Multifamiliares', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oficinas', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oportunidades de Negocio', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Terrenos Comerciales', 'parent_id' => $CRcomercialId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Especialidades principales (padres)
        DB::table('specialities')->insert([
            ['name' => 'Hacienda/Quinta', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Industrial', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Locales Comerciales', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Multifamiliares', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oficinas', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oportunidades de Negocio', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Terrenos Comerciales', 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Obtener los IDs de las especialidades padres
        $haciendaQuintaId = DB::table('specialities')->where('name', 'Hacienda/Quinta')->value('id');
        $industrialId = DB::table('specialities')->where('name', 'Industrial')->value('id');
        $localesComercialesId = DB::table('specialities')->where('name', 'Locales Comerciales')->value('id');
        $multifamiliaresId = DB::table('specialities')->where('name', 'Multifamiliares')->value('id');
        $oficinasId = DB::table('specialities')->where('name', 'Oficinas')->value('id');
        $terrenosComercialesId = DB::table('specialities')->where('name', 'Terrenos Comerciales')->value('id');

        // Subespecialidades de Hacienda/Quinta
        DB::table('specialities')->insert([
            ['name' => 'Agrícola', 'parent_id' => $haciendaQuintaId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ganadería', 'parent_id' => $haciendaQuintaId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mixto', 'parent_id' => $haciendaQuintaId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Industrial
        DB::table('specialities')->insert([
            ['name' => 'Almacen Suelto', 'parent_id' => $industrialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bodega', 'parent_id' => $industrialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'I&D', 'parent_id' => $industrialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manufactura', 'parent_id' => $industrialId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Parque Industrial', 'parent_id' => $industrialId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Locales Comerciales
        DB::table('specialities')->insert([
            ['name' => 'Oficina', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Centro Comercial', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'En el Barrio', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'En la Comunidad', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Especialidad', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Local Grande', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Suelto', 'parent_id' => $localesComercialesId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Multifamiliares
        DB::table('specialities')->insert([
            ['name' => 'Apartamentos', 'parent_id' => $multifamiliaresId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Condominio/Conjunto', 'parent_id' => $multifamiliaresId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cooperativas', 'parent_id' => $multifamiliaresId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Parques de Casas Rodantes', 'parent_id' => $multifamiliaresId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casas para Retirados', 'parent_id' => $multifamiliaresId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Oficinas
        DB::table('specialities')->insert([
            ['name' => 'En Edificio Alta Densidad', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'En Edificio Baja Densidad', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'En Edificio Mediana Densidad', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Complejo de Oficinas', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Condominio de Oficinas', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casa hecha Oficina', 'parent_id' => $oficinasId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Subespecialidades de Terrenos Comerciales
        DB::table('specialities')->insert([
            ['name' => 'Para Desarrollo', 'parent_id' => $terrenosComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mejorado', 'parent_id' => $terrenosComercialesId, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sin Mejoras', 'parent_id' => $terrenosComercialesId, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Insertar la especialidad padre RESResidencial
        $resResidencialId = DB::table('specialities')->insertGetId([
            'name' => 'RESResidencial',
            'parent_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Subespecialidades relacionadas con RESResidencial
        $subSpecialities = [
            'Comunidades mayores',
            'Oportunidades de negocio',
            'Ventas cortas',
            'Ventas fuertes',
            'Administración de Bienes',
            'Alquiler',
            'Casas de Lujo',
            'Compradores',
            'Condominios',
            'Construcciones Nuevas',
            'Desarrollo de Terrenos',
            'Haciendas y Terrenos',
            'Internacional (transacciones en el extranjero)',
            'Inversión',
            'Militar',
            'Propiedad con Caballos',
            'Propiedades al Lago',
            'Propiedades embargadas',
            'Propiedades Vacacionales  Resorts y Fraternidades',
            'Quintas Residenciales',
            'Remates',
            'Reubicaciones',
            'Tiempo Compartido',
        ];

        // Insertar las subespecialidades
        foreach ($subSpecialities as $subSpeciality) {
            DB::table('specialities')->insert([
                'name' => $subSpeciality,
                'parent_id' => $resResidencialId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Obtener el ID del área RES
        $resAreaId = DB::table('areas')->where('name', 'RES')->value('id');

        // Relacionar RESResidencial con el área RES
        DB::table('area_specialities')->insert([
            'area_id' => $resAreaId,
            'speciality_id' => $resResidencialId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}
