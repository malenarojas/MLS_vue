<?php

use App\Http\Controllers\OptionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/options'], function () {
    Route::get('/get-departamentos', [OptionController::class, 'getDepartamentos']);
    Route::post('/get-provincias', [OptionController::class, 'getProvincias']);
    Route::post('/get-ciudades', [OptionController::class, 'getCiudades']);
    Route::post('/get-zonas', [OptionController::class, 'getZonas']);
    Route::get('/get-transacciones', [OptionController::class, 'getListingTransactionType']);
    Route::get('/get-subtipo', [OptionController::class, 'getSubType']);
    Route::get('/get-contrato', [OptionController::class, 'getContract']);
    Route::post('/get-select', [OptionController::class, 'getSelectOptions']);
    Route::get('/get-lang', [OptionController::class, 'getLang']);
    Route::get('/get-agents', [OptionController::class, 'getAgents']);
    Route::post('/get-categories', [OptionController::class, 'getCategories']);
});
