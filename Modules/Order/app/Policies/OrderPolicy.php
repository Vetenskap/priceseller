<?php

namespace Modules\Order\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Order\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->organization->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->organization->user_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->id === $order->organization->user_id;
    }

    public function restore(User $user, Order $order): bool
    {
        return $user->id === $order->organization->user_id;
    }

    public function forceDelete(User $user, Order $order): bool
    {
        return $user->id === $order->organization->user_id;
    }
}
