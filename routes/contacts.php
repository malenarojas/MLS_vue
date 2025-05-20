<?php

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/contacts', 'middleware' => [
        'auth:sanctum'
    ]], function () {
    Route::get('/', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contacts.edit');
    Route::get('/search', [ContactController::class, 'search']);
    Route::get('/{id}', [ContactController::class, 'show']);
    Route::post('/store', [ContactController::class, 'store']);
	Route::put('/{id}', [ContactController::class, 'update']);
   // Route::get('/migrateContactsGyAPI', [ContactController::class, 'migrateContactsGyAPI']);
});

