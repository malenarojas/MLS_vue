<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{

    public function index()
    {
        $languages = Language::select('id', 'name', 'code', 'is_default')
            ->orderBy('is_default', 'desc')
            ->get();

        return response()->json($languages);
    }
}
