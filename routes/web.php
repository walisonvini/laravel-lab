<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CustomerController;

Route::prefix('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });

});
