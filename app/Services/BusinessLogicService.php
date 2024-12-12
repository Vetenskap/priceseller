<?php

namespace App\Services;

use App\Jobs\Email\CheckEmails;
use App\Jobs\Supplier\UnloadOnTime;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class BusinessLogicService
{
    public static function usersEmailsUnload(): void
    {
        User::whereHas('emails', function (Builder $query) {
            $query->where('open', true);
        })->chunk(5, function (Collection $users) {
            $users->filter(fn(User $user) => $user->isSub() || $user->isAdmin())->each(function (User $user) {
                CheckEmails::dispatch($user);
            });
        });

        $time = now()->format('i');

//        if ($time === "00") {
//            Supplier::where('open', true)
//                ->where('unload_without_price', true)
//                ->chunk(5, function (Collection $suppliers) {
//                    $suppliers->filter(fn (Supplier $supplier) => $supplier->user->isSub() || $supplier->user->isAdmin())->each(function (Supplier $supplier) {
//                        UnloadOnTime::dispatch($supplier);
//                    });
//                });
//        }
    }
}
