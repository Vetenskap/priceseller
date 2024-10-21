<?php

namespace App\Policies;

use App\Models\Bundle;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BundlePolicy
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
    public function view(User|Employee $user, Bundle $bundle): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $bundle->user_id) {
                return $user->can('view-bundles');
            }
            return false;
        } else {
            return $user->id === $bundle->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User|Employee $user): bool
    {
        if ($user instanceof Employee) {
            return $user->can('create-bundles');
        } else {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Employee $user, Bundle $bundle): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $bundle->user_id) {
                return $user->can('update-bundles');
            }
            return false;
        } else {
            return $user->id === $bundle->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Employee $user, Bundle $bundle): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $bundle->user_id) {
                return $user->can('delete-bundles');
            }
            return false;
        } else {
            return $user->id === $bundle->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User|Employee $user, Bundle $bundle): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $bundle->user_id) {
                return $user->can('restore-bundles');
            }
            return false;
        } else {
            return $user->id === $bundle->user_id;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User|Employee $user, Bundle $bundle): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $bundle->user_id) {
                return $user->can('forceDelete-bundles');
            }
            return false;
        } else {
            return $user->id === $bundle->user_id;
        }
    }
}
