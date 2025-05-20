<?php

use App\Http\Controllers\BankController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/banks'], function () {


    Route::get('/get-all',[BankController::class, 'getAll']);

    // Protected Routes
    Route::group([
        "middleware" => ["auth:api"]
    ], function () {});
});
