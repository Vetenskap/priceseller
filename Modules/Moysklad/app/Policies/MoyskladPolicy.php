<?php

namespace Modules\Moysklad\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Moysklad\Models\Moysklad;

class MoyskladPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ?Moysklad $moysklad = null): bool
    {
        if ($moysklad) {
            return $user->id === $moysklad->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Moysklad $moysklad): bool
    {
        return $user->id === $moysklad->user_id;
    }

    public function delete(User $user, Moysklad $moysklad): bool
    {
        return $user->id === $moysklad->user_id;
    }

    public function restore(User $user, Moysklad $moysklad): bool
    {
        return $user->id === $moysklad->user_id;
    }

    public function forceDelete(User $user, Moysklad $moysklad): bool
    {
        return $user->id === $moysklad->user_id;
    }
}
