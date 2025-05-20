<?php

use App\Http\Controllers\CommissionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/commissions'], function () {


    Route::post('/create-update',[ CommissionController::class, 'createUpdate']);

    // Protected Routes
    // Route::group([
    //     "middleware" => ["auth:api"]
    // ], function () {});
});
