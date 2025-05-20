<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Province;


class ProvinceController extends Controller
{
    public function index(Request $request)
    {
        $stateId = $request->input('state_id');

        $provinces = Province::select('id', 'name')
            ->when($stateId, function ($query) use ($stateId) {
                return $query->where('state_id', $stateId);
            })->get();

        return response()->json($provinces);
    }
}
