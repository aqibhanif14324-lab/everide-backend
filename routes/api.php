<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ShippingController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\VariantController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CsrfCookieController;

// Public routes - CSRF cookie endpoint (needs session middleware)
Route::middleware('api.session')->group(function () {
    Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
});

// Auth routes - need session middleware for login/register/logout
Route::middleware('api.session')->prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

// Protected routes - require authentication via Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);

    // Buyer abilities
    Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'store']);
    Route::post('/payments/{orderId}/intent', [PaymentController::class, 'createIntent']);
    Route::post('/shops', [ShopController::class, 'store']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    // Seller abilities
    Route::middleware('seller')->group(function () {
        // Listings
        Route::post('/listings', [ListingController::class, 'store']);
        Route::put('/listings/{id}', [ListingController::class, 'update']);
        Route::delete('/listings/{id}', [ListingController::class, 'destroy']);
        Route::post('/listings/{id}/publish', [ListingController::class, 'publish']);
        Route::post('/listings/{id}/archive', [ListingController::class, 'archive']);
        Route::post('/listings/{id}/options', [ListingController::class, 'attachOptions']);
        Route::post('/listings/{id}/option-values', [ListingController::class, 'attachOptionValues']);
        Route::post('/listings/{id}/variants', [ListingController::class, 'createVariant']);
        Route::get('/listings/{id}/variants', [ListingController::class, 'getVariants']);

        // Variants
        Route::put('/variants/{id}', [VariantController::class, 'update']);
        Route::post('/variants/{id}/images', [VariantController::class, 'addImage']);
        Route::delete('/variants/{id}/images/{imageId}', [VariantController::class, 'deleteImage']);

        // Shop management
        Route::put('/shops/{id}', [ShopController::class, 'update']);
        Route::delete('/shops/{id}', [ShopController::class, 'destroy']);
        Route::get('/shops/{id}/settings', [ShopController::class, 'getSettings']);
        Route::put('/shops/{id}/settings', [ShopController::class, 'updateSettings']);

        // Orders management
        Route::post('/orders/{id}/status', [OrderController::class, 'updateStatus']);

        // Shipping
        Route::get('/shipping/pickups', [ShippingController::class, 'getPickups']);
        Route::post('/shipping/{orderId}/label', [ShippingController::class, 'createLabel']);
        Route::get('/shipping/{orderId}/tracking', [ShippingController::class, 'getTracking']);
    });

    Route::middleware('admin')->group(function () {
        // Reserved for admin-only API endpoints
    });
});

// Public listings routes
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{slug}', [ListingController::class, 'show']);

// Public shops routes
Route::get('/shops', [ShopController::class, 'index']);
Route::get('/shops/{slug}/listings', [ShopController::class, 'listings']);
Route::get('/shops/{slug}', [ShopController::class, 'show']);

// Webhooks (public, but signature verified)
Route::post('/webhooks/payments', [\App\Http\Controllers\Api\WebhookController::class, 'handlePayment']);
