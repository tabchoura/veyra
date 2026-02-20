<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\FiberController;
use App\Http\Controllers\YarnController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\AccessoryController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\EndOfLifeController;
use App\Http\Controllers\UsageController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\AccessoryTypeController;
use App\Http\Controllers\YarnTypeController;
use App\Http\Controllers\ColouringMethodController;
use App\Http\Controllers\FinishingMethodController;
use App\Http\Controllers\FinishTreatmentController;
use App\Http\Controllers\FabricConstructionMethodController;
use App\Http\Controllers\SpinningMethodController;
use App\Http\Controllers\FabricTypeController;

use App\Http\Controllers\BawearController;

use App\Http\Controllers\EnvironmentalSummaryController;
use App\Http\Controllers\PassportGenerationController;
use App\Http\Controllers\PublicDppController;
use App\Http\Controllers\PassportProgressController;

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
// PUBLIC ROUTES
// ============================================
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);

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
    // REFERENCE DATA
    // ============================================
    Route::get('/materials', [FiberController::class, 'getMaterials']);
    Route::get('/yarn-types', [YarnTypeController::class, 'index']);
    Route::get('/fabric-types', [FabricTypeController::class, 'index']);
    Route::get('/colouring-methods', [ColouringMethodController::class, 'index']);
    Route::get('/finishing-methods', [FinishingMethodController::class, 'index']);
    Route::get('/finish-treatments', [FinishTreatmentController::class, 'index']);
    Route::get('/fabric-construction-methods', [FabricConstructionMethodController::class, 'index']);
    Route::get('/spinning-methods', [SpinningMethodController::class, 'index']);

    // ============================================
    // PRODUCTS - CRUD
    // ============================================
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // ============================================
    // VOLET 1 - Initialisation produit
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
    // VOLET 3 - Fibers
    // ============================================
    Route::prefix('products/{productId}')->group(function () {
        Route::get('/fibers', [FiberController::class, 'index']);
        Route::post('/fibers', [FiberController::class, 'store']);
        Route::put('/fibers/{fiberId}', [FiberController::class, 'update']);
        Route::delete('/fibers/{fiberId}', [FiberController::class, 'destroy']);

        Route::post('/fibers/save-progress', [FiberController::class, 'saveProgress']);
        Route::post('/fibers/validate-step', [FiberController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 4 - Yarns
    // ============================================
    Route::prefix('products/{productId}/yarns')->group(function () {
        Route::get('/', [YarnController::class, 'index']);
        Route::post('/', [YarnController::class, 'store']);
        Route::post('/save-progress', [YarnController::class, 'saveProgress']);
        Route::post('/validate-step', [YarnController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 5 - Fabrics
    // ============================================
    Route::prefix('products/{productId}/fabrics')->group(function () {
        Route::get('/', [FabricController::class, 'index']);
        Route::post('/', [FabricController::class, 'store']);
        Route::put('/{fabricId}', [FabricController::class, 'update']);
        Route::delete('/{fabricId}', [FabricController::class, 'destroy']);
        Route::post('/save-progress', [FabricController::class, 'saveProgress']);
        Route::post('/validate-step', [FabricController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 6 - Manufacturing
    // ============================================
    Route::prefix('products/{productId}/manufacturings')->group(function () {
        Route::get('/', [ManufacturingController::class, 'index']);
        Route::post('/', [ManufacturingController::class, 'store']);
        Route::put('/{manufacturingId}', [ManufacturingController::class, 'update']);
        Route::delete('/{manufacturingId}', [ManufacturingController::class, 'destroy']);

        Route::post('/save-progress', [ManufacturingController::class, 'saveProgress']);
        Route::post('/validate-step', [ManufacturingController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 7 - Accessories
    // ============================================
    Route::get('/accessory-types', [AccessoryTypeController::class, 'index']);
    Route::prefix('products/{productId}/accessories')->group(function () {
        Route::get('/', [AccessoryController::class, 'index']);
        Route::post('/', [AccessoryController::class, 'store']);
        Route::put('/{accessoryId}', [AccessoryController::class, 'update']);
        Route::delete('/{accessoryId}', [AccessoryController::class, 'destroy']);

        Route::post('/save-progress', [AccessoryController::class, 'saveProgress']);
        Route::post('/validate-step', [AccessoryController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 8 - Usage & Repairs
    // ============================================
    Route::prefix('products/{productId}/usage')->group(function () {
        Route::get('/', [UsageController::class, 'show']);
        Route::post('/', [UsageController::class, 'upsert']);
        Route::post('/save-progress', [UsageController::class, 'saveProgress']);
        Route::post('/validate-step', [UsageController::class, 'validateStep']);

        Route::get('/repairs', [RepairController::class, 'index']);
        Route::post('/repairs', [RepairController::class, 'store']);
        Route::put('/repairs/{repairId}', [RepairController::class, 'update']);
        Route::delete('/repairs/{repairId}', [RepairController::class, 'destroy']);
    });

    // ============================================
    // VOLET 9 - End Of Life
    // ============================================
    Route::prefix('products/{productId}/end-of-life')->group(function () {
        Route::get('/', [EndOfLifeController::class, 'show']);
        Route::post('/', [EndOfLifeController::class, 'storeOrUpdate']);
        Route::post('/validate-step', [EndOfLifeController::class, 'validateStep']);
    });

    // ============================================
    // VOLET 10 - bAwear Assessment
    // ============================================
    Route::get('/products/{product}/bawear', [BawearController::class, 'showLatest']);
    Route::post('/products/{product}/bawear/pdf', [BawearController::class, 'uploadPdf']);
    Route::patch('/products/{product}/bawear/{assessment}', [BawearController::class, 'updateNormalized'])
        ->whereNumber('assessment');

    // futur : bAwear API (Volet 11)
    Route::post('/products/{product}/bawear/calculate', [BawearController::class, 'calculateFromApi']);

    // ============================================
    // VOLET 12 - Environmental Summary
    // ============================================
    Route::prefix('products/{productId}/environmental-summary')->group(function () {
        Route::get('/', [EnvironmentalSummaryController::class, 'show']);
        Route::post('/save-progress', [EnvironmentalSummaryController::class, 'saveProgress']);
    });

    // ============================================
    // VOLET 13 - Generate Passport
    // ============================================
    Route::prefix('products/{productId}/passport')->group(function () {
        Route::get('/generation', [PassportGenerationController::class, 'show']);
        Route::post('/save-progress', [PassportGenerationController::class, 'saveProgress']);
        Route::post('/publish', [PassportGenerationController::class, 'publish']);
        // Route publique (sans auth) sera ajoutée séparément si nécessaire
    });
});

// ============================================
// PUBLIC DPP ROUTE (outside auth middleware)
// ============================================
Route::get('/public/dpp/{token}', [PublicDppController::class, 'show']);


Route::middleware('auth:sanctum')->post(
    '/passports/{productId}/save-progress',
    [PassportProgressController::class, 'save']
);