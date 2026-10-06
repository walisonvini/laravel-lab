<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\AuthController;

Route::prefix('api')->group(function () {

    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });
    
});
