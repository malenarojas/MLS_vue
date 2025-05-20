<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;

use App\Http\Controllers\OfficeController;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('offices')->group(function () {
    // Routes de las
    Route::get('/', [OfficeController::class, "index"])->name('offices.index'); // Lista todas las officinas
    Route::get('/all', [OfficeController::class, 'getAllOffice']);
    Route::get('/filter', [OfficeController::class, 'getFilteredOffices'])->name('offices.filter');
    Route::get('/edit', [OfficeController::class, 'edit'])->name('offices.edit');
    Route::get("/{id}", [OfficeController::class, "show"]); // obtiene una officina especifica
    Route::post('/', [OfficeController::class, "store"])->name('offices.store'); // Crea una officina
    Route::post("/{id}", [OfficeController::class, "update"])->name(name: 'offices.update'); // actualiza una officina
    Route::delete("/{id}", [OfficeController::class, "destroy"]); // elimina una officina
    
    Route::post("/get-ranking", [OfficeController::class, "getRanking"]); // elimina una officina
});
