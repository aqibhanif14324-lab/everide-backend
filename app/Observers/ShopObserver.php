<?php

namespace App\Observers;

use App\Events\ShopApproved;
use App\Models\Shop;

class ShopObserver
{
    public function creating(Shop $shop): void
    {
        if ($shop->status === 'approved' && ! $shop->approved_at) {
            $shop->approved_at = now();
        }
    }

    public function created(Shop $shop): void
    {
        if ($shop->status === 'approved') {
            ShopApproved::dispatch($shop);
        }
    }

    public function updating(Shop $shop): void
    {
        if ($shop->isDirty('status')) {
            if ($shop->status === 'approved' && ! $shop->approved_at) {
                $shop->approved_at = now();
            }

            if ($shop->status !== 'approved') {
                $shop->approved_at = null;
            }
        }
    }

    public function updated(Shop $shop): void
    {
        if ($shop->wasChanged('status') && $shop->status === 'approved') {
            ShopApproved::dispatch($shop);
        }
    }
}

