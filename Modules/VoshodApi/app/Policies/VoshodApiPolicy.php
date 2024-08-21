<?php

namespace Modules\VoshodApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\VoshodApi\Models\VoshodApi;

class VoshodApiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?VoshodApi $voshodApi = null): bool
    {
        if ($voshodApi) {
            return $user->id === $voshodApi->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, VoshodApi $voshodApi): bool
    {
        return $user->id === $voshodApi->user_id;
    }

    public function delete(User $user, VoshodApi $voshodApi): bool
    {
        return $user->id === $voshodApi->user_id;
    }

    public function restore(User $user, VoshodApi $voshodApi): bool
    {
        return $user->id === $voshodApi->user_id;
    }

    public function forceDelete(User $user, VoshodApi $voshodApi): bool
    {
        return $user->id === $voshodApi->user_id;
    }
}
