<?php

namespace App\Policies;

use App\Models\Bid;
use App\Models\User;
use App\Models\WorkOrder;

class BidPolicy
{
    /**
     * Determine whether the user can view any bids.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the bid.
     * Users can view their own bids or bids on work orders they own.
     */
    public function view(User $user, Bid $bid): bool
    {
        return $user->id === $bid->user_id ||
               $user->id === $bid->workOrder->owner_id;
    }

    /**
     * Determine whether the user can create bids.
     * Any authenticated user can bid (except on their own work orders - checked in controller).
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the bid.
     * Only the bidder can update their pending bid.
     */
    public function update(User $user, Bid $bid): bool
    {
        return $user->id === $bid->user_id && $bid->status === 'pending';
    }

    /**
     * Determine whether the user can accept the bid.
     * Only the work order owner can accept bids.
     */
    public function accept(User $user, Bid $bid): bool
    {
        return $user->id === $bid->workOrder->owner_id &&
               $bid->status === 'pending' &&
               $bid->workOrder->status === 'open';
    }

    /**
     * Determine whether the user can reject the bid.
     * Only the work order owner can reject bids.
     */
    public function reject(User $user, Bid $bid): bool
    {
        return $user->id === $bid->workOrder->owner_id &&
               $bid->status === 'pending';
    }

    /**
     * Determine whether the user can delete the bid.
     * Only the bidder can delete their pending bid.
     */
    public function delete(User $user, Bid $bid): bool
    {
        return $user->id === $bid->user_id && $bid->status === 'pending';
    }

    /**
     * Determine whether the user can restore the bid.
     */
    public function restore(User $user, Bid $bid): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the bid.
     */
    public function forceDelete(User $user, Bid $bid): bool
    {
        return false;
    }
}
