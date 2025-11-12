<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBuyer();
    }

    public function view(User $user, Order $order): bool
    {
        $shopOwnerId = $order->shop?->owner_id;

        return $user->isAdmin()
            || $order->buyer_id === $user->id
            || ($user->isSeller() && $shopOwnerId === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->isBuyer();
    }

    public function update(User $user, Order $order): bool
    {
        $shopOwnerId = $order->shop?->owner_id;

        return $user->isSeller() && $shopOwnerId === $user->id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->isAdmin();
    }

    public function updateStatus(User $user, Order $order): bool
    {
        $shopOwnerId = $order->shop?->owner_id;

        return $user->isSeller() && $shopOwnerId === $user->id;
    }
}