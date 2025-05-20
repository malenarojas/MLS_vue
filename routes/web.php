<?php

use App\Http\Controllers\Listings\ListingController;
use App\Http\Controllers\LoginAsController;
use App\Http\Controllers\Listings\LocationController;
use App\Http\Controllers\RoleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__ . '/dashboard.php';
require __DIR__ . '/office.php';
require __DIR__ . '/transactions.php';
require __DIR__ . '/payments.php';
require __DIR__ . '/agent.php';
require __DIR__ . '/bank.php';
require __DIR__ . '/commissions.php';
require __DIR__ . '/contacts.php';
require __DIR__ . '/listing.php';
require __DIR__ . '/reports.php';
require __DIR__ . '/marketanalysis.php';
require __DIR__ . '/teammanagement.php';
require __DIR__ . '/qualitycontrol.php';
require __DIR__ . '/migration.php';
require __DIR__ . '/options.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/goal.php';
require __DIR__ . '/intranet.php';

Route::get('/', function () {
    return redirect()->route('login');
    // return Inertia::render('Welcome', [
    //     'canLogin' => Route::has('login'),
    //     'canRegister' => Route::has('register'),
    //     'laravelVersion' => Application::VERSION,
    //     'phpVersion' => PHP_VERSION,
    // ]);
});

Route::middleware([
    'auth:sanctum',
    'auth:sanctum',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard/index');
    })->name('dashboard');

    Route::post('/login-as', [LoginAsController::class, 'store'])->name('login-as.store');
    Route::post('/back-to-account', [LoginAsController::class, 'destroy'])->name('login-as.destroy');
    // Route::group(['prefix' => 'listings', 'as' => 'listings.'], function () {
    //     Route::get('/', [ListingController::class, 'index'])->name('index');
    //     Route::post('/', [ListingController::class, 'store'])->name('store');
    //     Route::get('/edit/{key}', [ListingController::class, 'edit'])->name('edit');
    //     // Route::post('/edit/{key}', [ListingController::class, 'edit'])->name('edit');
    //     Route::post('/{key}', [ListingController::class, 'update'])->name('update');
    //     // Route::post('/{key}', [ListingController::class, 'updateTest'])->name('update.test');
    // });
});

//Roles
Route::controller(RoleController::class)->group(function () {
    Route::get('/roles/{id}', 'edit')->name('roles.edit')->middleware('auth');
    Route::post('/roles/{id}', 'update')->name('roles.update')->middleware('auth');
    Route::get('/roles', 'index')->name('roles.index')->middleware('auth');
});
