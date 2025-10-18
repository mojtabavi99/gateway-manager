<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Site\SiteController;

require_once 'partials/transaction.php';

Route::get('/', [SiteController::class, 'index'])->name('home');
