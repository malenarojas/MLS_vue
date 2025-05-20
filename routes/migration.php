<?php

use App\Http\Controllers\Migration\ListingMigrationController;
use Illuminate\Support\Facades\Route;

Route::group([
  'prefix' => '/migrations',
  'as' => 'migrations.',
  'middleware' => [
    'auth:sanctum'
  ]
], function () {
  Route::group([
    'prefix' => '/listings',
    'as' => 'listings.',
  ], function () {
    Route::post('/excel', [ListingMigrationController::class, 'migrateFrom'])->name('excel');
    Route::get('/', [ListingMigrationController::class, 'index'])->name('index');
    Route::post('/', [ListingMigrationController::class, 'store'])->name('store');

    Route::get('/insert', [ListingMigrationController::class, 'indexInsert'])->name('indexInsert');
    Route::post('/insert', [ListingMigrationController::class, 'storeInsert'])->name('storeInsert');

    Route::get('/report', [ListingMigrationController::class, 'indexReport'])->name('indexReport');
    Route::post('/report', [ListingMigrationController::class, 'migrateFromReport'])->name('storeReport');
  });
});
