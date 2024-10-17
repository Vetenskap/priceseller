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
        if (get_class($user) === Employee::class) {
            return $user->user->id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function create(User|Employee $user): bool
    {
        return true;
    }

    public function update(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if (get_class($user) === Employee::class) {
            return $user->user->id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function delete(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if (get_class($user) === Employee::class) {
            return $user->user->id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function restore(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if (get_class($user) === Employee::class) {
            return $user->user->id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }

    public function forceDelete(User|Employee $user, OzonMarket $ozonMarket): bool
    {
        if (get_class($user) === Employee::class) {
            return $user->user->id === $ozonMarket->user_id;
        } else {
            return $user->id === $ozonMarket->user_id;
        }
    }
}
