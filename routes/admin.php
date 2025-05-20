<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['auth:sanctum', 'HasAdminMenuPermission']], function () {
    
    Route::get('/',[AdminController::class,'index'])->name('admin.index');
    Route::post('/exchange-rate',[AdminController::class,'exchangeRate'])->name('admin.exchangeRate');
    Route::post('/submissions',[AdminController::class,'submissions'])->name('admin.submissions');
    
});
