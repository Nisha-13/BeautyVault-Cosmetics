<?php

use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReturnController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SkinDiagnosticController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Health & System Info
    Route::get('/health', [AdminDashboardController::class, 'healthCheck']);

    // Public Catalog & Metadata
    Route::get('/metadata', [ProductController::class, 'metadata']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::post('/match-shades', [ProductController::class, 'matchShades']);

    // Beauty Diagnostics & Quiz
    Route::get('/diagnostic/quiz', [SkinDiagnosticController::class, 'getQuizQuestions']);
    Route::post('/diagnostic/quiz', [SkinDiagnosticController::class, 'submitQuiz']);

    // Ingredient Glossary
    Route::get('/ingredients', [IngredientController::class, 'index']);
    Route::get('/ingredients/{slug}', [IngredientController::class, 'show']);

    // Public Order Tracking
    Route::post('/orders/track', [OrderController::class, 'track']);

    // Cart Endpoints (Works for both Authenticated and Guest sessions via X-Session-ID)
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getCart']);
        Route::post('/items', [CartController::class, 'addItem']);
        Route::patch('/items/{id}', [CartController::class, 'updateQuantity']);
        Route::delete('/items/{id}', [CartController::class, 'removeItem']);
        Route::post('/coupon', [CartController::class, 'applyCoupon']);
        Route::delete('/coupon', [CartController::class, 'removeCoupon']);
    });

    // Public Checkout (Guests or Authenticated)
    Route::post('/checkout', [CheckoutController::class, 'process']);

    // Auth Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);

    // Customer Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::put('/auth/skin-profile', [AuthController::class, 'updateSkinProfile']);
        Route::post('/auth/profile', [AuthController::class, 'updateProfile']);
        Route::get('/diagnostic/my-routine', [SkinDiagnosticController::class, 'getMyRoutine']);

        // Orders & Tracking
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);

        // Reviews
        Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
        Route::post('/reviews/{id}/upvote', [ReviewController::class, 'upvote']);

        // Returns
        Route::get('/returns', [ReturnController::class, 'index']);
        Route::post('/returns', [ReturnController::class, 'requestReturn']);

        // Wishlist
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);
        Route::delete('/wishlist/{productId}', [WishlistController::class, 'destroy']);

        // Admin Protected Routes
        Route::prefix('admin')->middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->group(function () {
            Route::get('/analytics', [AdminDashboardController::class, 'analytics']);
            Route::get('/orders', [AdminDashboardController::class, 'orders']);
            Route::patch('/orders/{id}/status', [AdminDashboardController::class, 'updateOrderStatus']);
            Route::get('/inventory', [AdminDashboardController::class, 'inventory']);
            Route::get('/batches', [AdminDashboardController::class, 'batches']);
            Route::post('/batches/restock', [AdminDashboardController::class, 'restock']);
            Route::get('/returns', [AdminDashboardController::class, 'returns']);
            Route::patch('/returns/{id}/status', [AdminDashboardController::class, 'updateReturnStatus']);
        });
    });
});
