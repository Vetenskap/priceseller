<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OzonMarketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User|Employee $user): bool
    {
        return true;
    }

    public function view(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $ozonMarket->user_id) {
                return $user->can('view-ozon');
            }
            return false;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function create(User|Employee $user): bool
    {
        if ($user instanceof Employee) {
            return $user->can('create-ozon');
        }
        return true;
    }

    public function update(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $ozonMarket->user_id) {
                return $user->can('update-ozon');
            }
            return false;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function delete(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $ozonMarket->user_id) {
                return $user->can('delete-ozon');
            }
            return false;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function restore(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function forceDelete(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }
}
