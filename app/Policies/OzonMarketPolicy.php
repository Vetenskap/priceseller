<?php

namespace App\Policies;

use App\Models\OzonMarket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\App;

class OzonMarketPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, OzonMarket $ozonMarket): bool
    {
        return $user->id === $ozonMarket->user_id;
    }

    public function create(User $user): bool
    {
        if (App::isLocal() || $user->isAdmin()) return true;

        $count = $user->ozonMarkets()->count();

        if ($count >= 5 && !$user->isOzonTenSub()) {
            return false;
        }

        return true;
    }

    public function update(User $user, OzonMarket $ozonMarket): bool
    {
        return $user->id === $ozonMarket->user_id;
    }

    public function delete(User $user, OzonMarket $ozonMarket): bool
    {
        return $user->id === $ozonMarket->user_id;
    }

    public function restore(User $user, OzonMarket $ozonMarket): bool
    {
        return $user->id === $ozonMarket->user_id;
    }

    public function forceDelete(User $user, OzonMarket $ozonMarket): bool
    {
        return $user->id === $ozonMarket->user_id;
    }
}
