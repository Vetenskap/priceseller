<?php

namespace App\Services;

use App\Jobs\Email\CheckEmails;
use App\Jobs\Supplier\UnloadOnTime;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

        if ($time === "30") {
            $offset = Cache::get('supplier-unload_without_price_offset', 0);
            if ($offset > Supplier::where('open', true)->where('unload_without_price', true)->count()) {
                $offset = 0;
            }
            Supplier::where('open', true)
                ->where('unload_without_price', true)
                ->offset($offset)
                ->limit(2)
                ->get()
                ->each(function (Collection $suppliers) {
                    $suppliers->filter(fn (Supplier $supplier) => $supplier->user->isSub() || $supplier->user->isAdmin())->each(function (Supplier $supplier) {
                        UnloadOnTime::dispatch($supplier);
                    });
                });
            Cache::set('supplier-unload_without_price_offset', $offset + 3);
        }
    }
}
