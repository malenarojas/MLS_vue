<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Route::post('/change-user-password', [AdminController::class, 'changeUserPassword'])
    //->middleware('auth:sanctum')
  //  ->name('change-user-password');
