<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
        Route::post('/loginadmin', [AuthController::class, 'loginadmin']);

    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});



Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index' ]);
    Route::patch('/users/{user}/approve', [UserController::class, 'approve']);
    Route::patch('/users/{user}/reject', [UserController::class, 'reject']);
});

Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);