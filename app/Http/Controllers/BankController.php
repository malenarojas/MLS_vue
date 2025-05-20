<?php

namespace App\Http\Controllers;

use App\Models\Bank;
// use Illuminate\Http\Request;

class BankController extends Controller
{
    public function getAll()
    {
        return response()->json(Bank::all());
    }
}