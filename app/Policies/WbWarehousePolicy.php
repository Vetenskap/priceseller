<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WbWarehouse;
use Illuminate\Auth\Access\Response;

class WbWarehousePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, WbWarehouse $wbWarehouse): bool
    {
        return $user->id === $wbWarehouse->market->user_id;
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
    public function update(User $user, WbWarehouse $wbWarehouse): bool
    {
        return $user->id === $wbWarehouse->market->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, WbWarehouse $wbWarehouse): bool
    {
        return $user->id === $wbWarehouse->market->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, WbWarehouse $wbWarehouse): bool
    {
        return $user->id === $wbWarehouse->market->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, WbWarehouse $wbWarehouse): bool
    {
        return $user->id === $wbWarehouse->market->user_id;
    }
}
