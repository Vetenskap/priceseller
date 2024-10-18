<?php

namespace App\Policies;

use App\Models\EmailSupplierStockValue;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmailSupplierStockValuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User|Employee $user, EmailSupplierStockValue $emailSupplierStockValue): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        } else {
            return $user->id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        }
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User|Employee $user, EmailSupplierStockValue $emailSupplierStockValue): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        } else {
            return $user->id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Employee $user, EmailSupplierStockValue $emailSupplierStockValue): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        } else {
            return $user->id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User|Employee $user, EmailSupplierStockValue $emailSupplierStockValue): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        } else {
            return $user->id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User|Employee $user, EmailSupplierStockValue $emailSupplierStockValue): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        } else {
            return $user->id === $emailSupplierStockValue->emailSupplier->supplier->user_id;
        }
    }
}
