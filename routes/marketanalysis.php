<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MarketAnalysisController;

Route::middleware([
    'auth:sanctum'
])->prefix('marketanalysis')->group(function () {

    // Vista principal del análisis de mercado
    Route::get('/', [MarketAnalysisController::class, 'index'])
        ->name('marketanalysis.index');


    // Filtro de propiedades
    Route::post('/search', [MarketAnalysisController::class, 'filterListings'])
        ->name('marketanalysis.search');

    // Búsqueda por coordenadas (sin recargar la vista)
    Route::post('/search-by-coordinates', [MarketAnalysisController::class, 'searchByCoordinates'])
        ->name('marketanalysis.searchcoordinates');

    // Generación de PDF para caso uno
    Route::post('/imprimir/caso_one', [MarketAnalysisController::class, 'generatePdfcaseone'])
        ->name('marketanalysis.pdf.caseone');

    // Generación de PDF para caso dos
    Route::post('/imprimir/caso_two', [MarketAnalysisController::class, 'generatePdfcasetwo'])
        ->name('marketanalysis.pdf.casetwo');

    // Obtener todos los listings
    Route::get('/show', [MarketAnalysisController::class, 'getAlllisting'])
        ->name('marketanalysis.show');
});




