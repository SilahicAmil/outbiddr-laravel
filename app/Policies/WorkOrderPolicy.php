<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Auth\Access\Response;

class WorkOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WorkOrder $workOrder)
    {
        return $user->id === $workOrder->owner_id
            || $user->id === $workOrder->assigned_user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $user->id === $workOrder->owner_id
            || $user->id === $workOrder->assigned_user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return $user->id === $workOrder->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }
}
