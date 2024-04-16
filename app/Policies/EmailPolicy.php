<?php

namespace App\Policies;

use App\Models\Email;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    public function delete(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    public function restore(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }

    public function forceDelete(User $user, Email $email): bool
    {
        return $user->id === $email->user_id;
    }
}
