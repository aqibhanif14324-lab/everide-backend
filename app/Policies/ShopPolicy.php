<?php

namespace App\Policies;

use App\Models\Shop;
use App\Models\User;

class ShopPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Public shops
    }

    public function view(?User $user, Shop $shop): bool
    {
        if ($shop->status === 'approved') {
            return true;
        }
        
        if (! $user) {
            return false;
        }

        return $user->isAdmin() || $shop->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isBuyer();
    }

    public function update(User $user, Shop $shop): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($shop->owner_id !== $user->id) {
            return false;
        }

        return $user->isSeller() || $shop->status === 'pending';
    }

    public function delete(User $user, Shop $shop): bool
    {
        return $user->isAdmin() || $shop->owner_id === $user->id;
    }

    public function restore(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Shop $shop): bool
    {
        return $user->isAdmin();
    }
}