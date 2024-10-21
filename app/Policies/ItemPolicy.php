<?php

namespace App\Policies;

use App\Models\Employee;
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

    public function view(User|Employee $user, Item $item): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $item->user_id) {
                return $user->can('view-items');
            }
            return false;
        } else {
            return $user->id === $item->user_id;
        }
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User|Employee $user, Item $item): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $item->user_id) {
                return $user->can('update-items');
            }
            return false;
        } else {
            return $user->id === $item->user_id;
        }
    }

    public function delete(User|Employee $user, Item $item): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $item->user_id) {
                return $user->can('delete-items');
            }
            return false;
        } else {
            return $user->id === $item->user_id;
        }
    }

    public function restore(User|Employee $user, Item $item): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $item->user_id;
        } else {
            return $user->id === $item->user_id;
        }
    }

    public function forceDelete(User|Employee $user, Item $item): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $item->user_id;
        } else {
            return $user->id === $item->user_id;
        }
    }
}
