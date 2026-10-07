<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ProductController;

Route::prefix('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');

        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

});
