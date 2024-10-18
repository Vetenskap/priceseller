<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User|Employee $user, Supplier $supplier): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $supplier->user_id) {
                return $user->can('view-suppliers');
            }
            return false;
        } else {
            return $user->id === $supplier->user_id;
        }
    }

    public function create(User|Employee $user): bool
    {
        if ($user instanceof Employee) {
            return $user->can('create-suppliers');
        }
        return true;
    }

    public function update(User|Employee $user, Supplier $supplier): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $supplier->user_id) {
                return $user->can('update-suppliers');
            }
            return false;
        } else {
            return $user->id === $supplier->user_id;
        }
    }

    public function delete(User|Employee $user, Supplier $supplier): bool
    {
        if ($user instanceof Employee) {
            if ($user->user_id === $supplier->user_id) {
                return $user->can('delete-suppliers');
            }
            return false;
        } else {
            return $user->id === $supplier->user_id;
        }
    }

    public function restore(User|Employee $user, Supplier $supplier): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $supplier->user_id;
        } else {
            return $user->id === $supplier->user_id;
        }
    }

    public function forceDelete(User|Employee $user, Supplier $supplier): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $supplier->user_id;
        } else {
            return $user->id === $supplier->user_id;
        }
    }
}
