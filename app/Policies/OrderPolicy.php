<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator() || $user->isUser();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isModerator() || $order->buyer_id === $user->id || $order->shop->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUser() || $user->isModerator() || $user->isAdmin();
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isModerator() || $order->shop->owner_id === $user->id;
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
        return $user->isAdmin() || $user->isModerator() || $order->shop->owner_id === $user->id;
    }
}