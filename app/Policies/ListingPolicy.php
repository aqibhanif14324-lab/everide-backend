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
        
        if (!$user) {
            return false;
        }

        return $user->isAdmin() || $user->isModerator() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isUser() || $user->isModerator() || $user->isAdmin();
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $user->isModerator() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
    }

    public function restore(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    public function forceDelete(User $user, Listing $listing): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $user->isModerator() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
    }

    public function archive(User $user, Listing $listing): bool
    {
        return $user->isAdmin() || $user->isModerator() || $listing->user_id === $user->id || $listing->shop->owner_id === $user->id;
    }
}