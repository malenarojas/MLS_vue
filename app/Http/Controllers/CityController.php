<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $provinceId = $request->input('province_id');
        $cities = City::select('id', 'name', 'latitude', 'longitude')
            ->when($provinceId, function ($query) use ($provinceId) {
                return $query->where('province_id', $provinceId);
            })
            ->get();

        return response()->json($cities);
    }
}
