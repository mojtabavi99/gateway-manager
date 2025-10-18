<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\TransactionController;

Route::prefix('transaction')->name('transaction.')
    ->group(function () {
        Route::controller(TransactionController::class)
            ->name('site.')
            ->group(function () {
                //
                Route::post('/deposit', 'deposit')->name('deposit');
                Route::get('/{transaction}/initiate', 'initiatePayment')->name('initiate_payment');
                Route::get('/{transaction}/verify', 'verifyPayment')->name('verify_payment');
                Route::get('/{transaction}/result', 'paymentResult')->name('payment_result');
            });
    });
