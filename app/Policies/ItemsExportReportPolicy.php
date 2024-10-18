<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\ItemsExportReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ItemsExportReportPolicy
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
    public function view(User|Employee $user, ItemsExportReport $itemsExportReport): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        } else {
            return $user->id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
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
    public function update(User|Employee $user, ItemsExportReport $itemsExportReport): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        } else {
            return $user->id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        }
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User|Employee $user, ItemsExportReport $itemsExportReport): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        } else {
            return $user->id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        }
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User|Employee $user, ItemsExportReport $itemsExportReport): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        } else {
            return $user->id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        }
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User|Employee $user, ItemsExportReport $itemsExportReport): bool
    {
        if ($user instanceof Employee) {
            return $user->user_id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        } else {
            return $user->id === (int) ($itemsExportReport->reportable->user_id ?? $itemsExportReport->reportable->id);
        }
    }
}
