<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index () {
        return Inertia::render('Admin/index', [
            'exchange' => ExchangeRate::orderBy('id', 'desc')->first()?->amount??6.96
        ]);
    }

    public function exchangeRate (Request $request){
        
        ExchangeRate::create([
            'amount' => $request->exchange,
            'user_id' => auth()->user()->id,
            'date' => now()
        ]);

        return response()->json(['message' => 'Exchange rate updated successfully'], Response::HTTP_OK);
    }

    public function changeUserPassword (Request $request) {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => ['required', 'string', 'min:8'],
        ]);
    
        $user = User::findOrFail($validated['user_id']);
        $user->password = bcrypt($validated['new_password']);
        $user->save();

        $user->syncRoles(['Agente']);

        return response()->json(['message' => 'Password changed successfully'], Response::HTTP_OK);
    }
}
