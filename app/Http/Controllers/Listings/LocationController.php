<?php

namespace App\Http\Controllers\Listings;

use App\Dtos\Listings\LocationDto;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Province;
use App\Models\Zone;
use App\Traits\HasLocationData;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    use HasLocationData;

    public function updateLocation(Request $request)
    {
        $locationDto = LocationDto::from($request->all());
        $locationData = $this->getLocationData($locationDto);
        $existingData = session()->get('inertia_data', []);

        return Inertia::render('Listings/Edit', array_merge($existingData, [
            'provinces' => $locationData['provinces'],
            'cities' => $locationData['cities'],
            'zones' => $locationData['zones'],
        ]));
    }
}
