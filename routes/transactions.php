<?php

use App\Http\Controllers\TransactionController;
use App\Models\Transaction;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'transactions', 'middleware' => [
        'auth:sanctum',
    ]], function () {
    // Open Routes/transaction/update
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/{transaction}/show', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/get-transaction/{id}', [TransactionController::class, 'getDetalle']);
    Route::get('/update-status/{transaction_id}/{status_id}', [TransactionController::class, 'updateStatus']);
    Route::get('/get-transaction/{id}/pdf', [TransactionController::class, 'generatePdf']);
    Route::get('/get-transaction/{id}/pdfdown', [TransactionController::class, 'generatePdfDownload']);
    Route::get('/get-transaction-statuses', [TransactionController::class, 'getTransactionStatues']);
    Route::post('/set-finantiation', [TransactionController::class, 'setFinantiation']);
    Route::post('/get-transactiones', [TransactionController::class, 'getTransacciones']);
    Route::get('/fixExcelImportation', [TransactionController::class, 'fixExcelImportation']);

    Route::post('/store', [TransactionController::class, 'store']);
    Route::get('/create', [TransactionController::class, 'create']);
    Route::get('/get-step-data', [TransactionController::class, 'getStepData']);
    Route::post('/update', [TransactionController::class, 'update']);

    Route::get('/importFromExcel', [TransactionController::class, 'importFromExcel']);

    // Protected Routes
    Route::group([
        "middleware" => ["auth:api"]
    ], function () {});
});
