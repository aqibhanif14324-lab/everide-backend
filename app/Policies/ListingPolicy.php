<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Public listings
    }

    public function view(?User $user, Listing $listing): bool
    {
        if ($listing->status === 'published') {
            return true;
        }
        
        if (! $user) {
            return false;
        }

        $shopOwnerId = $listing->shop?->owner_id;

        return $user->isSeller() || $listing->user_id === $user->id || $shopOwnerId === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isSeller();
    }

    public function update(User $user, Listing $listing): bool
    {
        $shopOwnerId = $listing->shop?->owner_id;

        return $user->isSeller() && ($listing->user_id === $user->id || $shopOwnerId === $user->id);
    }

    public function delete(User $user, Listing $listing): bool
    {
        $shopOwnerId = $listing->shop?->owner_id;

        return $user->isSeller() && ($listing->user_id === $user->id || $shopOwnerId === $user->id);
    }

    public function restore(User $user, Listing $listing): bool
    {
        return $user->isSeller();
    }

    public function forceDelete(User $user, Listing $listing): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Listing $listing): bool
    {
        $shopOwnerId = $listing->shop?->owner_id;

        return $user->isSeller() && ($listing->user_id === $user->id || $shopOwnerId === $user->id);
    }

    public function archive(User $user, Listing $listing): bool
    {
        $shopOwnerId = $listing->shop?->owner_id;

        return $user->isSeller() && ($listing->user_id === $user->id || $shopOwnerId === $user->id);
    }
}