<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use App\Models\Zone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateCityAndZoneCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statesData = [
            1 => ['name' => 'Cochabamba', 'lat' => -17.38282033, 'lng' => -66.15972899],
            2 => ['name' => 'La Paz', 'lat' => -16.49586465, 'lng' => -68.1212847],
            3 => ['name' => 'Santa Cruz', 'lat' => -17.78300061, 'lng' => -63.18216249],
            4 => ['name' => 'Tarija', 'lat' => -21.51706173, 'lng' => -64.73912707],
            6 => ['name' => 'Oruro', 'lat' => -17.97100742, 'lng' => -67.09564153],
            7 => ['name' => 'Potosi', 'lat' => -19.57095895, 'lng' => -65.75532371],
            8 => ['name' => 'Beni', 'lat' => -14.83098673, 'lng' => -64.9032304],
            11 => ['name' => 'Chuquisaca', 'lat' => -19.03521545, 'lng' => -65.25813732],
            14 => ['name' => 'Pando', 'lat' => -11.02461825, 'lng' => -68.76605804],
        ];

        DB::transaction(function () use ($statesData) {
            foreach ($statesData as $stateId => $data) {
                $lat = $data['lat'];
                $lng = $data['lng'];
                $stateName = $data['name'];

                $provinceIds = Province::where('state_id', $stateId)->pluck('id');

                City::whereIn('province_id', $provinceIds)->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);

                $cityIds = City::whereIn('province_id', $provinceIds)->pluck('id');

                Zone::whereIn('city_id', $cityIds)->update([
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]);

                $this->command->info("✅ Coordenadas aplicadas para el estado '{$stateName}'");
            }
        });
    }
}
