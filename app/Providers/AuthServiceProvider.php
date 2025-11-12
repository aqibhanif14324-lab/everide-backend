<?php

namespace App\Providers;

use App\Models\Listing;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Listing::class => \App\Policies\ListingPolicy::class,
        Order::class => \App\Policies\OrderPolicy::class,
        Shop::class => \App\Policies\ShopPolicy::class,
        User::class => \App\Policies\UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::before(function (?User $user) {
            if ($user?->isAdmin()) {
                return true;
            }

            return null;
        });

        Gate::define('listing.view', fn (User $user) => $user->isBuyer());
        Gate::define('order.create', fn (User $user) => $user->isBuyer());
        Gate::define('order.pay', function (User $user, Order $order): bool {
            return $user->isBuyer() && $order->buyer_id === $user->id;
        });

        Gate::define('listing.create', fn (User $user) => $user->isSeller());

        Gate::define('listing.update', function (User $user, Listing $listing): bool {
            return $user->isSeller() && (
                $listing->user_id === $user->id ||
                $listing->shop?->owner_id === $user->id
            );
        });

        Gate::define('listing.archive', function (User $user, Listing $listing): bool {
            return $user->isSeller() && (
                $listing->user_id === $user->id ||
                $listing->shop?->owner_id === $user->id
            );
        });

        Gate::define('order.manage_own', function (User $user, Order $order): bool {
            return $user->isSeller() && $order->shop?->owner_id === $user->id;
        });

        Gate::define('shop.manage_own', function (User $user, Shop $shop): bool {
            return $user->isSeller() && $shop->owner_id === $user->id;
        });

        Gate::define('admin.access', fn (User $user) => $user->isAdmin());
    }
}

