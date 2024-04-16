<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ItemPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    public function restore(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }

    public function forceDelete(User $user, Item $item): bool
    {
        return $user->id === $item->user_id;
    }
}
