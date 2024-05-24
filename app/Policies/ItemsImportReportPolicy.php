<?php

namespace App\Policies;

use App\Models\ItemsImportReport;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ItemsImportReportPolicy
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
    public function view(User $user, ItemsImportReport $itemsImportReport): bool
    {
        return $user->id === ($itemsImportReport->reportable->user_id ?? $itemsImportReport->reportable->id);
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
    public function update(User $user, ItemsImportReport $itemsImportReport): bool
    {
        return $user->id === ($itemsImportReport->reportable->user_id ?? $itemsImportReport->reportable->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ItemsImportReport $itemsImportReport): bool
    {
        return $user->id === ($itemsImportReport->reportable->user_id ?? $itemsImportReport->reportable->id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ItemsImportReport $itemsImportReport): bool
    {
        return $user->id === ($itemsImportReport->reportable->user_id ?? $itemsImportReport->reportable->id);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ItemsImportReport $itemsImportReport): bool
    {
        return $user->id === ($itemsImportReport->reportable->user_id ?? $itemsImportReport->reportable->id);
    }
}
