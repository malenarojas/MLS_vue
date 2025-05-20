<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/reports', 'middleware' => [
    'auth:sanctum'
]], function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/transaction-payment', [ReportController::class, 'transactionPayment'])->name('reports.transaction-payment');
    Route::get('/listings', [ReportController::class, 'listings'])->name('reports.listings');
});