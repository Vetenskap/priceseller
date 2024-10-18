<?php

namespace App\Policies;

use App\Models\Email;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User|Employee $user, Email $email): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $email->user_id;
        } else {
            return $user->id === $email->user_id;
        }
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User|Employee $user, Email $email): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $email->user_id;
        } else {
            return $user->id === $email->user_id;
        }
    }

    public function delete(User|Employee $user, Email $email): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $email->user_id;
        } else {
            return $user->id === $email->user_id;
        }
    }

    public function restore(User|Employee $user, Email $email): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $email->user_id;
        } else {
            return $user->id === $email->user_id;
        }
    }

    public function forceDelete(User|Employee $user, Email $email): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $email->user_id;
        } else {
            return $user->id === $email->user_id;
        }
    }
}
