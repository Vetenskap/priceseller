<?php

namespace Modules\SamsonApi\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\SamsonApi\Models\SamsonApi;

class SamsonApiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?SamsonApi $samsonApi = null): bool
    {
        if ($samsonApi) {
            return $user->id === $samsonApi->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SamsonApi $samsonApi): bool
    {
        return $user->id === $samsonApi->user_id;
    }

    public function delete(User $user, SamsonApi $samsonApi): bool
    {
        return $user->id === $samsonApi->user_id;
    }

    public function restore(User $user, SamsonApi $samsonApi): bool
    {
        return $user->id === $samsonApi->user_id;
    }

    public function forceDelete(User $user, SamsonApi $samsonApi): bool
    {
        return $user->id === $samsonApi->user_id;
    }
}
