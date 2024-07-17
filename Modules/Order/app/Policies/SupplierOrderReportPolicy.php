<?php

namespace Modules\Order\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Order\Models\SupplierOrderReport;

class SupplierOrderReportPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, SupplierOrderReport $report): bool
    {
        return $user->id === $report->supplier->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, SupplierOrderReport $report): bool
    {
        return $user->id === $report->supplier->user_id;
    }

    public function delete(User $user, SupplierOrderReport $report): bool
    {
        return $user->id === $report->supplier->user_id;
    }

    public function restore(User $user, SupplierOrderReport $report): bool
    {
        return $user->id === $report->supplier->user_id;
    }

    public function forceDelete(User $user, SupplierOrderReport $report): bool
    {
        return $user->id === $report->supplier->user_id;
    }
}
