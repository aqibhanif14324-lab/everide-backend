<?php

namespace App\Providers;

use App\Events\ShopApproved;
use App\Listeners\PromoteShopOwnerToSeller;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        ShopApproved::class => [
            PromoteShopOwnerToSeller::class,
        ],
    ];
}

