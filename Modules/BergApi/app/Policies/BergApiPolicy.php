<?php

namespace Modules\BergApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\BergApi\Models\BergApi;

class BergApiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BergApi $bergApi = null): bool
    {
        if ($bergApi) {
            return $user->id === $bergApi->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, BergApi $bergApi): bool
    {
        return $user->id === $bergApi->user_id;
    }

    public function delete(User $user, BergApi $bergApi): bool
    {
        return $user->id === $bergApi->user_id;
    }

    public function restore(User $user, BergApi $bergApi): bool
    {
        return $user->id === $bergApi->user_id;
    }

    public function forceDelete(User $user, BergApi $bergApi): bool
    {
        return $user->id === $bergApi->user_id;
    }
}
