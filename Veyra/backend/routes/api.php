<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\FiberController;

// ============================================
// AUTH ROUTES
// ============================================
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/loginadmin', [AuthController::class, 'loginadmin']);

    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerification']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// ============================================
// PUBLIC ROUTES (Reference data)
// ============================================
// ✅ Une seule source de vérité pour les pays
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

// ✅ Matériaux/fibres: public si tu veux remplir des selects
Route::get('/materials', [FiberController::class, 'getMaterials']);

// ============================================
// ADMIN ROUTES (protégées)
// ============================================
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::patch('/users/{user}/approve', [UserController::class, 'approve']);
    Route::patch('/users/{user}/reject', [UserController::class, 'reject']);
});

// ============================================
// PROTECTED ROUTES (AUTH REQUIRED)
// ============================================
Route::middleware('auth:sanctum')->group(function () {

    // ============================================
    // PRODUCTS - CRUD
    // ============================================
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // ============================================
    // VOLET 1 - Initialisation du produit
    // ============================================
    Route::post('/products/save-progress', [ProductController::class, 'saveProgress']);
    Route::post('/products/{id}/complete-volet1', [ProductController::class, 'completeVolet1']);

    // ============================================
    // VOLET 2 - Type de produit
    // ============================================
  
Route::get('/categories', [ProductTypeController::class, 'getCategories']);

Route::get('/products/{productId}/type', [ProductTypeController::class, 'getProductType']);
Route::post('/products/{productId}/type/save-progress', [ProductTypeController::class, 'saveProgress']);
Route::post('/products/{productId}/type/validate-step', [ProductTypeController::class, 'validateStep']);
    // ============================================
    // VOLET 3 - Composition des fibres
    // ============================================
   
Route::prefix('products/{productId}')->group(function () {
    Route::get('fibers', [FiberController::class, 'index']);
    Route::post('fibers', [FiberController::class, 'store']);
    Route::put('fibers/{fiberId}', [FiberController::class, 'update']);
    Route::delete('fibers/{fiberId}', [FiberController::class, 'destroy']);

    Route::post('fibers/save-progress', [FiberController::class, 'saveProgress']);
    Route::post('fibers/validate-step', [FiberController::class, 'validateStep']);
});

Route::get('materials', [FiberController::class, 'getMaterials']);
Route::get('countries', [FiberController::class, 'getCountries']);


});
