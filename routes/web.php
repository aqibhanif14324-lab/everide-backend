<?php

use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Admin Login Routes - with session middleware
Route::middleware('web')->prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('auth.logout');
    });
});

// Filament expects a logout route with the name `filament.admin.auth.logout`.
// Register an explicit route name (no 'admin.' prefix) that Filament will call when logging out.
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->middleware(['web', 'auth'])
    ->name('filament.admin.auth.logout');

require __DIR__.'/auth.php';
