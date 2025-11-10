<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Register Gates
        \Illuminate\Support\Facades\Gate::define('manageUsers', function ($user) {
            return $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('moderateListings', function ($user) {
            return $user->isAdmin() || $user->isModerator();
        });

        \Illuminate\Support\Facades\Gate::define('manageShopOwn', function ($user, $shop) {
            return $user->isAdmin() || $shop->owner_id === $user->id;
        });

        \Illuminate\Support\Facades\Gate::define('buy', function ($user) {
            return $user->isUser() || $user->isModerator() || $user->isAdmin();
        });

        \Illuminate\Support\Facades\Gate::define('manageListingOwn', function ($user, $listing) {
            return $user->isAdmin() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
        });

        \Illuminate\Support\Facades\Gate::define('manageOrderOwn', function ($user, $order) {
            return $user->isAdmin() || $order->buyer_id === $user->id || $order->shop->owner_id === $user->id;
        });
    }
}
