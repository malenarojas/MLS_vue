<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->input('city_id');

        $zones = Zone::select('id', 'name', 'latitude', 'longitude')
            ->when($cityId, function ($query) use ($cityId) {
                return $query->where('city_id', $cityId);
            })->get();

        return response()->json($zones);
    }
}
