<?php

use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/dashboard', 'middleware' => [
        'auth:sanctum'
    ]], function () {

    Route::get('', [MenuController::class, 'getMenuItems']);
    Route::post('/get-datos-charts', [MenuController::class, 'getDatosCharts']);
    Route::post('/get-resumen-ejecutivo', [MenuController::class, 'getResumenEjecutivo']);
    Route::post('/get-transacciones-tipos', [MenuController::class, 'getDatosTransaccionesPorTipos']);
    Route::post('/get-transacciones', [MenuController::class, 'getDatosTransacciones']);
    Route::post('/get-detalles-transacciones', [MenuController::class, 'getDetallesTransacciones']);
    Route::post('/captaciones', [MenuController::class, 'getDatosCaptaciones']);

    Route::post('/get-departamentos', [MenuController::class, 'getDepartamentos']);
    Route::post('/get-provincias', [MenuController::class, 'getProvincias']);
    Route::post('/get-ciudades', [MenuController::class, 'getCiudades']);
    Route::post('/get-zonas', [MenuController::class, 'getZonas']);

    Route::post('/get-oficinas-por-ubicacion', [MenuController::class, 'getOficinasPorUbicacion']);
    Route::post('/get-agentes-por-ubicacion', [MenuController::class, 'getAgentesPorUbicacion']);

    Route::post('/get-agentes', [MenuController::class, 'getAgentes']);
    Route::post('/get-datos-secundarios', [MenuController::class, 'getDatosSecundarios']);

    Route::post('/get-inventario', [MenuController::class, 'getInventario']);
    Route::post('/get-promedios', [MenuController::class, 'getPromedios']);

    Route::post('/get-comisiones', [MenuController::class, 'getComisiones']);
    Route::get('/test', [MenuController::class, 'test']);

    Route::get('/transacciones', [MenuController::class, 'transactions']);
    Route::get('/transactions/get-transactions-by-type', [MenuController::class, 'getTransactionsByType']);

    Route::get('/executive-resume/details', [MenuController::class, 'executiveResumeDetails'])->name('executive-resume.details');
    Route::get('/executive-resume/details/export', [MenuController::class, 'executiveResumeDetailsExport'])->name('executive-resume.details.export');
});
