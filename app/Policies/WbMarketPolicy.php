<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WbMarket;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\App;

class WbMarketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, WbMarket $wbMarket): bool
    {
        return $user->id === $wbMarket->user_id;
    }

    public function create(User $user): bool
    {
        if (App::isLocal() || $user->isAdmin()) return true;

        $count = $user->wbMarkets()->count();

        if ($count >= 5 && !$user->isWbTenSub()) {
            return false;
        }

        return true;
    }

    public function update(User $user, WbMarket $wbMarket): bool
    {
        return $user->id === $wbMarket->user_id;
    }

    public function delete(User $user, WbMarket $wbMarket): bool
    {
        return $user->id === $wbMarket->user_id;
    }

    public function restore(User $user, WbMarket $wbMarket): bool
    {
        return $user->id === $wbMarket->user_id;
    }

    public function forceDelete(User $user, WbMarket $wbMarket): bool
    {
        return $user->id === $wbMarket->user_id;
    }
}
