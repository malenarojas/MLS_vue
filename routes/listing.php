<?php

use App\Http\Controllers\Listings\ListingController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/listings',
    'as' => 'listings.',
    'middleware' => [
        'auth:sanctum'
    ]
], function () {
    Route::get('web/{key}', [ListingController::class, 'web'])->name('web');
    Route::get('/get-transaction-types', [ListingController::class, 'getTransactionTypes']);
    Route::post('/update-buyers', [ListingController::class, 'updateBuyers']);
    Route::get('/filter-data', [ListingController::class, 'indexFilter']);

    Route::get('/', [ListingController::class, 'index'])->name('index');
    Route::post('/', [ListingController::class, 'store'])->name('store');
    Route::get('/edit/{key}', [ListingController::class, 'edit'])->name('edit');
    Route::post('/external', [ListingController::class, 'storeExternal']);

    Route::post('/{key}', [ListingController::class, 'update'])->name('update');
    Route::post('/draft/{key}', [ListingController::class, 'updateDraft'])->name('update.draft');
    Route::post('/copy/{key}', [ListingController::class, 'copy'])->name('copy');

    // Reportes
    Route::get('/generate-excel', [ListingController::class, 'downloadExcel'])->name('download.excel');
    Route::get('/download-pdf/{key}', [ListingController::class, 'downloadPdf'])->name('download.pdf');
});
