<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public routes - CSRF cookie endpoint (needs session middleware)
Route::middleware('api.session')->group(function () {
    Route::get('/sanctum/csrf-cookie', function () {
        return response()->json(['message' => 'CSRF cookie set']);
    });
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

    // Listings
    Route::post('/listings', [\App\Http\Controllers\Api\ListingController::class, 'store']);
    Route::put('/listings/{id}', [\App\Http\Controllers\Api\ListingController::class, 'update']);
    Route::delete('/listings/{id}', [\App\Http\Controllers\Api\ListingController::class, 'destroy']);
    Route::post('/listings/{id}/publish', [\App\Http\Controllers\Api\ListingController::class, 'publish']);
    Route::post('/listings/{id}/archive', [\App\Http\Controllers\Api\ListingController::class, 'archive']);
    Route::post('/listings/{id}/options', [\App\Http\Controllers\Api\ListingController::class, 'attachOptions']);
    Route::post('/listings/{id}/option-values', [\App\Http\Controllers\Api\ListingController::class, 'attachOptionValues']);
    Route::post('/listings/{id}/variants', [\App\Http\Controllers\Api\ListingController::class, 'createVariant']);
    Route::get('/listings/{id}/variants', [\App\Http\Controllers\Api\ListingController::class, 'getVariants']);

    // Variants
    Route::put('/variants/{id}', [\App\Http\Controllers\Api\VariantController::class, 'update']);
    Route::post('/variants/{id}/images', [\App\Http\Controllers\Api\VariantController::class, 'addImage']);
    Route::delete('/variants/{id}/images/{imageId}', [\App\Http\Controllers\Api\VariantController::class, 'deleteImage']);

    // Shops
    Route::apiResource('shops', \App\Http\Controllers\Api\ShopController::class);
    Route::get('/shops/{slug}/listings', [\App\Http\Controllers\Api\ShopController::class, 'listings']);
    Route::get('/shops/{id}/settings', [\App\Http\Controllers\Api\ShopController::class, 'getSettings']);
    Route::put('/shops/{id}/settings', [\App\Http\Controllers\Api\ShopController::class, 'updateSettings']);

    // Orders
    Route::apiResource('orders', \App\Http\Controllers\Api\OrderController::class);
    Route::post('/orders/{id}/status', [\App\Http\Controllers\Api\OrderController::class, 'updateStatus']);

    // Payments
    Route::post('/payments/{orderId}/intent', [\App\Http\Controllers\Api\PaymentController::class, 'createIntent']);

    // Shipping
    Route::get('/shipping/pickups', [\App\Http\Controllers\Api\ShippingController::class, 'getPickups']);
    Route::post('/shipping/{orderId}/label', [\App\Http\Controllers\Api\ShippingController::class, 'createLabel']);
    Route::get('/shipping/{orderId}/tracking', [\App\Http\Controllers\Api\ShippingController::class, 'getTracking']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
});

// Public listings routes
Route::get('/listings', [\App\Http\Controllers\Api\ListingController::class, 'index']);
Route::get('/listings/{slug}', [\App\Http\Controllers\Api\ListingController::class, 'show']);

// Public shops routes
Route::get('/shops/{slug}', [\App\Http\Controllers\Api\ShopController::class, 'show']);

// Webhooks (public, but signature verified)
Route::post('/webhooks/payments', [\App\Http\Controllers\Api\WebhookController::class, 'handlePayment']);
