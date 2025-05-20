<?php

use App\Http\Controllers\Intranet\{ModuleController, ElementController};
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => '/intranets',
    'as' => 'intranets.',
    'middleware' => [
        'auth:sanctum'
    ]
], function () {
    Route::resource('modules', ModuleController::class)->only(['index', 'show']);
   // Route::get('modules/{module}', [ModuleController::class, 'show'])->name('intranets.modules.show');


    Route::group([
        'prefix' => '/elements',
        'as' => 'elements.',
    ], function () {
        // Route::get('/{element}', [ElementController::class, 'show'])->name('show');
        Route::get('redirect/{element}', [ElementController::class, 'redirectToView'])->name('redirect');
        // Route::get('/{element}/download', [ElementController::class, 'downloadVideo'])->name('download');
    });
});
