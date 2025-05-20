<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoalController;

Route::middleware(['auth:sanctum', 'HasGoalPermission'
])->prefix('goals')->group(function () {

    Route::get('/', [GoalController::class, 'index'])->name('goals.index');
    Route::get('/get-goals', [GoalController::class, 'getGoals'])->name('goals.getGoals');
    Route::put('/update-goal', [GoalController::class, 'update'])->name('goals.update');
});