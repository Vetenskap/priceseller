<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy
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
    public function view(User|Employee $user, Warehouse $warehouse): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $warehouse->user_id) {
                return $user->can('view-warehouses');
            }
            return false;
        } else {
            return $user->id === $warehouse->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Employee $user): bool
    {
        if ($user instanceof Employee) {
            return $user->can('create-warehouses');
        }
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Employee $user, Warehouse $warehouse): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $warehouse->user_id) {
                return $user->can('update-warehouses');
            }
            return false;
        } else {
            return $user->id === $warehouse->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Employee $user, Warehouse $warehouse): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $warehouse->user_id) {
                return $user->can('delete-warehouses');
            }
            return false;
        } else {
            return $user->id === $warehouse->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User|Employee $user, Warehouse $warehouse): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $warehouse->user_id;
        } else {
            return $user->id === $warehouse->user_id;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User|Employee $user, Warehouse $warehouse): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $warehouse->user_id;
        } else {
            return $user->id === $warehouse->user_id;
        }
    }
}
