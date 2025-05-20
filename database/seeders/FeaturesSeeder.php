<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturesSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      // Crear una característica principal
      $feature1 = Feature::create(['name' => 'Caracteristica Principales']);

      // Crear subcaracterísticas de `feature1`
      $subFeature1 = Feature::create(['name' => 'Terraza', 'feature_id' => $feature1->id]);
      $subFeature2 = Feature::create(['name' => 'Balcon', 'feature_id' => $feature1->id]);
      $subFeature3 = Feature::create(['name' => 'Sistema de Alarma', 'feature_id' => $feature1->id]);
      $subFeature4 = Feature::create(['name' => 'Piscina', 'feature_id' => $feature1->id]);
      $subFeature5 = Feature::create(['name' => 'Seguridad-Sistema', 'feature_id' => $feature1->id]);
      $subFeature6 = Feature::create(['name' => 'Parqueo', 'feature_id' => $feature1->id]);
      $subFeature7 = Feature::create(['name' => 'Acceso silla de ruedas', 'feature_id' => $feature1->id]);

      // Crear una característica principal
      $feature2 = Feature::create(['name' => 'Estilo Arquitectónico']);

      // Crear subcaracterísticas de `feature2`
      $subFeature8 = Feature::create(['name' => 'Casa', 'feature_id' => $feature2->id]);
      $subFeature9 = Feature::create(['name' => 'Edificio Nuevo', 'feature_id' => $feature2->id]);
      $subFeature10 = Feature::create(['name' => 'Edificio Antiguo', 'feature_id' => $feature2->id]);

      // Crear una característica principal adicional
      $feature3 = Feature::create(['name' => 'Características Exteriores']);

      // Crear subcaracterísticas de `feature2`
      $subFeature11 = Feature::create(['name' => 'Establo', 'feature_id' => $feature3->id]);
      $subFeature12 = Feature::create(['name' => 'Jardin', 'feature_id' => $feature3->id]);
      $subFeature13 = Feature::create(['name' => 'Estacionamiento', 'feature_id' => $feature3->id]);
      $subFeature14 = Feature::create(['name' => 'Garage con Puerta Automática', 'feature_id' => $feature3->id]);

      // Crear una característica principal adicional
      $feature4 = Feature::create(['name' => 'Características Exteriores']);

      // Crear subcaracterísticas de `feature3`
      $subFeature15 = Feature::create(['name' => 'Establo', 'feature_id' => $feature4->id]);
      $subFeature16 = Feature::create(['name' => 'Jardin', 'feature_id' => $feature4->id]);
      $subFeature17 = Feature::create(['name' => 'Estacionamiento', 'feature_id' => $feature4->id]);
      $subFeature18 = Feature::create(['name' => 'Garage con Puerta Automática', 'feature_id' => $feature4->id]);



      // Crear una característica principal adicional
      $feature5 = Feature::create(['name' => 'Características Interior']);

      // Crear subcaracterísticas de `feature4`
      $subFeature28 = Feature::create(['name' => 'Aire Acondicionado Central', 'feature_id' => $feature5->id]);
      $subFeature29 = Feature::create(['name' => 'Cocina', 'feature_id' => $feature5->id]);
      $subFeature30 = Feature::create(['name' => 'Galpón', 'feature_id' => $feature5->id]);
      $subFeature31 = Feature::create(['name' => 'Lavandería (en edificio)', 'feature_id' => $feature5->id]);
      $subFeature32 = Feature::create(['name' => 'Sauna', 'feature_id' => $feature5->id]);
      $subFeature33 = Feature::create(['name' => 'Amoblado', 'feature_id' => $feature5->id]);
      $subFeature34 = Feature::create(['name' => 'Cuarto de Juegos', 'feature_id' => $feature5->id]);
      $subFeature35 = Feature::create(['name' => 'Hall de Entrada', 'feature_id' => $feature5->id]);
      $subFeature36 = Feature::create(['name' => 'Paneles Solares', 'feature_id' => $feature5->id]);
      $subFeature37 = Feature::create(['name' => 'Fumar está permitido', 'feature_id' => $feature5->id]);
      $subFeature38 = Feature::create(attributes: ['name' => 'Sistema de Seguridad', 'feature_id' => $feature5->id]);
      $subFeature39 = Feature::create(attributes: ['name' => 'Baño de Servicio', 'feature_id' => $feature5->id]);
      $subFeature40 = Feature::create(attributes: ['name' => 'Lavandería (en depto.)', 'feature_id' => $feature5->id]);
      $subFeature41 = Feature::create(attributes: ['name' => 'Portero Eléctrico', 'feature_id' => $feature5->id]);
      $subFeature42 = Feature::create(attributes: ['name' => 'Vestidor', 'feature_id' => $feature5->id]);

      // Crear una característica principal adicional
      $feature6 = Feature::create(['name' => 'Tipo exterior']);

      // Crear subcaracterísticas de `feature4`
      $subFeature19 = Feature::create(['name' => 'Balcón Terraza', 'feature_id' => $feature6->id]);
      $subFeature20 = Feature::create(['name' => 'Madera', 'feature_id' => $feature6->id]);
      $subFeature21 = Feature::create(['name' => 'Tableta', 'feature_id' => $feature6->id]);
      $subFeature22 = Feature::create(['name' => 'Yeso', 'feature_id' => $feature6->id]);
      $subFeature23 = Feature::create(['name' => 'Paneles Externos', 'feature_id' => $feature6->id]);
      $subFeature24 = Feature::create(['name' => 'Techo Plano', 'feature_id' => $feature6->id]);
      $subFeature25 = Feature::create(['name' => 'Ladrillo', 'feature_id' => $feature6->id]);
      $subFeature26 = Feature::create(['name' => 'Piedra', 'feature_id' => $feature6->id]);
      $subFeature27 = Feature::create(['name' => 'Textura', 'feature_id' => $feature6->id]);

      // Crear una característica principal adicional
      $feature7 = Feature::create(['name' => 'general']);

      // Crear subcaracterísticas de `feature4`
      $subFeature43 = Feature::create(['name' => 'A estrenar', 'feature_id' => $feature7->id]);
      $subFeature44 = Feature::create(['name' => 'Conserje disponible', 'feature_id' => $feature7->id]);
      $subFeature45 = Feature::create(['name' => 'Ascensor', 'feature_id' => $feature7->id]);
      $subFeature46 = Feature::create(['name' => 'Communal Pool', 'feature_id' => $feature7->id]);


      // Crear el feature principal "Ubicación"
      $ubicacion = Feature::create(['name' => 'Ubicación']);


      // Crear subcaracterísticas para "Ubicación" exclusivamente de acuerdo con tu lista
      Feature::create(['name' => 'Area Escolar', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Casa de Campo', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Centro Histórico', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Cerca a Campo de Golf', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Cerca de Aeropuerto', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Cerca de Autopista', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Cerca de Avenida', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Comercios cercanos', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Estación de tren cerca', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Frente al Lago', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Gimnasio cercano', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Hospital cercano', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Iglesia cerca', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Plaza cerca', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Residencial', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Zona Concurrida', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Zona Tranquila', 'feature_id' => $ubicacion->id]);
      Feature::create(['name' => 'Zona Turística', 'feature_id' => $ubicacion->id]);

      // Crear el feature principal "Orientación"
      $orientacion = Feature::create(['name' => 'Orientación']);

      // Crear subcaracterísticas para "Orientación" exclusivamente de acuerdo con tu lista
      Feature::create(['name' => 'Este', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Noreste', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Noroeste', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Norte', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Oeste', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Sudeste', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Sur', 'feature_id' => $orientacion->id]);
      Feature::create(['name' => 'Suroeste', 'feature_id' => $orientacion->id]);

      // Crear el feature principal "Servicios Básicos"
      $serviciosBasicos = Feature::create(['name' => 'Servicios Básicos']);

      // Crear subcaracterísticas para "Servicios Básicos" exclusivamente de acuerdo con tu lista
      Feature::create(['name' => 'Acceso a Internet de alta velocidad (fibra óptica)', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Agua potable', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Aire Acondicionado', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Alcantarillado', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Balcón', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Cable', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Calefacción', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Cámara de seguridad', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Electric Vehicle Charge Station', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Electricidad', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Estufa a gas', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Estufa de calentamiento', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Gas', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Generador Eléctrico', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Generador Eléctrico de Emergencia', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Gimnasio', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Gimnasio de deportes', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Jacuzzi', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Medidor de Agua Individual', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Medidor de Gas Individual', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Paneles Solares', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Salida Emergencia', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Salida Emergencia Disponible', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Seguridad - Vigilancia', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Sin calefacción', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Tanque de Agua', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'TCP/IP', 'feature_id' => $serviciosBasicos->id]);
      Feature::create(['name' => 'Todo electrico', 'feature_id' => $serviciosBasicos->id]);


      // Crear el feature principal "Desarrollo del edificio"
      $desarrolloEdificio = Feature::create(['name' => 'Desarrollo del edificio Características Amenidades']);

      // Crear subcaracterísticas para "Desarrollo del edificio"
      Feature::create(['name' => 'Dispositivo de intercomunicación con monitor de televisión', 'feature_id' => $desarrolloEdificio->id]);
      Feature::create(['name' => 'Playa', 'feature_id' => $desarrolloEdificio->id]);
      Feature::create(['name' => 'Servicio de conserjería', 'feature_id' => $desarrolloEdificio->id]);

      // Crear el feature principal "LifeStyles"
      $lifeStyles = Feature::create(['name' => 'LifeStyles']);

      // Crear subcaracterísticas para "LifeStyles" exclusivamente de acuerdo con tu lista
      Feature::create(['name' => 'Frente a la playa', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Vida citadina', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Vida en el campo', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Golf', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Inversión', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Ranchos y Granjas', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Jubilación', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Estación de esquí', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Clima cálido', 'feature_id' => $lifeStyles->id]);
      Feature::create(['name' => 'Frente al mar', 'feature_id' => $lifeStyles->id]);
   }
}
