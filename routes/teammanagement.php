<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;

use App\Http\Controllers\TeamManagementController;
use App\Models\TeamManagement;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('teammanagement')->group(function () {

// Routes de las
Route::get('/', [TeamManagementController::class, "index"])->name('teammanagement.index'); // Lista todas las officinas
Route::get('/create', [TeamManagementController::class, "create"])->name('teammanagement.create'); // Lista todas las officinas
Route::get('/edit', [TeamManagementController::class, "edit"])->name('teammanagement.edit'); // Lista todas las officinas
Route::post("/{id}", [TeamManagementController::class, "update"])->name('teammanagement.update'); // actualiza una officina

//Route::get("/{id}", [TeamManagementController::class, "show"]); // obtiene una officina especifica
Route::post("", [TeamManagementController::class, "store"])->name('teammanagement.store'); // Crea una officina
Route::delete("/{id}", [TeamManagementController::class, "destroy"]); // elimina una officina
Route::get('/agents', [TeamManagementController::class, 'getAgents'])->name('teammanagement.agents');

});
