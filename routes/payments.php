<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/payments', 'middleware' => [
        'auth:sanctum'
    ]], function () {


    Route::post('/create-update',[ PaymentController::class, 'createUpdate']);
    Route::get('/get-payments-details',[ PaymentController::class, 'getPaymentsDetails']);
    Route::post('/update-payment',[ PaymentController::class, 'updatePayments']);
    Route::get('/get-payments-transaction', [PaymentController::class, 'getPaymentsTransaction']);
    Route::get('/get-payments-transaction-pdf-get/{start_date}/{end_date}/{office_id}/{inDollars}/{month}/{year}', [PaymentController::class, 'getPaymentsTransactionPdfGet']);
    Route::post('/get-payments-transaction-pdf', [PaymentController::class, 'getPaymentsTransactionPdf']);
    Route::get('/get-payments-transaction-excel/{office_id}/{start_date}/{end_date}/{inDollars}/{month}/{year}', [PaymentController::class, 'getPaymentsTransactionExcel']);
    Route::get('/get-only-payments-excel/{office_id}/{start_date}/{end_date}/{inDollars}/{month}/{year}', [PaymentController::class, 'getOnlyPaymentsExcel']);


    // Protected Routes
    Route::group([
        "middleware" => ["auth:api"]
    ], function () {});
});
