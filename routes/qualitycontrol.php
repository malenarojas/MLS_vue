<?php

use App\Http\Controllers\QualityController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/qualitycontrol',
    'as' => 'qualitycontrol.',
    'middleware' => [
        'auth:sanctum'
    ]
], function () {
    Route::get('/', [QualityController::class, 'index'])
        ->name('index');
    // ->middleware('hasDirectPermission:qualitycontrol.index');
    Route::post('/', [QualityController::class, 'store'])
        ->name('store');
});
