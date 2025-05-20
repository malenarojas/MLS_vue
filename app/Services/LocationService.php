<?php

namespace App\Services;

use App\Models\City;
use App\Models\Location;
use App\Models\Province;
use App\Models\State;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;

class LocationService
{

    public function getAllWithPagination(int $perPage = 10):Collection
    {
        return Location::paginate($perPage);
    }

    public function getAll():Collection
    {
        return Location::all();
    }
    public function getDepartamentos ($data) {
        //$ciudades = State::where('region_id', $data['region_id'])->get();
        $departamentos = State::all();
        return $departamentos;
    }

    public function getProvincias ($data) {
        return  Province::whereIn('state_id', $data['state_id'])->get();
    }

    public function getCiudades ($data) {
        return  City::whereIn('province_id', $data['province_id'])->get();
    }

    public function getZonas ($data) {
        return  Zone::whereIn('city_id', $data['city_id'])->get();
    }
}
