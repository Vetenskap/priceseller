<?php

namespace App\Services;

use App\Jobs\Email\CheckEmails;
use App\Jobs\Supplier\MarketsUnload;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class BusinessLogicService
{
    public static function usersEmailsUnload(): void
    {
        User::whereHas('emails', function (Builder $query) {
            $query->where('open', true);
        })->chunk(5, function (Collection $users) {
            $users->each(function (User $user) {
                if (App::isLocal()) {
                    CheckEmails::dispatchIf($user->isAdmin(), $user);
                } else {
                    CheckEmails::dispatchIf($user->isSub() || $user->isAdmin(), $user);
                }
            });
        });

        $time = now()->format('i');

        if ($time === "00") {
            Supplier::where('open', true)
                ->where('unload_without_price', true)
                ->chunk(5, function (Collection $suppliers) {
                    $suppliers->each(function (Supplier $supplier) {
                        SupplierService::setAllItemsUpdated($supplier);
                        MarketsUnload::dispatch($supplier->user, $supplier);
                    });
                });
        }
    }
}
