<?php

namespace App\Listeners;

use App\Events\ShopApproved;

class PromoteShopOwnerToSeller
{
    public function handle(ShopApproved $event): void
    {
        $owner = $event->shop->owner;

        if ($owner) {
            $owner->promoteToSeller();
        }
    }
}

