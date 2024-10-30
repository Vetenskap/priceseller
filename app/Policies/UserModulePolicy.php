<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Auth\Access\Response;

class UserModulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserModule $userModule): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Employee $user, UserModule $userModule): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $userModule->user_id) {
                return $user->can('update-modules');
            }
        } else {
            return $user->id === $userModule->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserModule $userModule): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserModule $userModule): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserModule $userModule): bool
    {
        //
    }
}
