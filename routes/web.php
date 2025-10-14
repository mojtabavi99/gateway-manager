<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;


Route::get('/', [SiteController::class, 'index'])->name('home');
Route::post('/', [SiteController::class, 'deposit'])->name('deposit');
Route::get('/{transaction}/register-transaction', [SiteController::class, 'registerTransaction'])->name('register-transaction');
Route::get('/transaction-callback', [SiteController::class, 'transactionCallback'])->name('transaction-callback');
