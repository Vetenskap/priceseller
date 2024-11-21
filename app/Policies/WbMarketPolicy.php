<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Auth\Access\HandlesAuthorization;

class WbMarketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User|Employee $user, WbMarket $wbMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $wbMarket->user_id) {
                return $user->can('view-wb');
            }
            return false;
        } else {
            return $user->id === $wbMarket->user_id;
        }
    }

    public function create(User|Employee $user): bool
    {

        if ($user instanceof Employee) {
            return $user->can('create-wb');
        }
        return true;
    }

    public function update(User|Employee $user, WbMarket $wbMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $wbMarket->user_id) {
                return $user->can('update-wb');
            }
            return false;
        } else {
            return $user->id === $wbMarket->user_id;
        }
    }

    public function delete(User|Employee $user, WbMarket $wbMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $wbMarket->user_id) {
                return $user->can('delete-wb');
            }
            return false;
        } else {
            return $user->id === $wbMarket->user_id;
        }
    }

    public function restore(User|Employee $user, WbMarket $wbMarket): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $wbMarket->user_id;
        } else {
            return $user->id === $wbMarket->user_id;
        }
    }

    public function forceDelete(User|Employee $user, WbMarket $wbMarket): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $wbMarket->user_id;
        } else {
            return $user->id === $wbMarket->user_id;
        }
    }
}
