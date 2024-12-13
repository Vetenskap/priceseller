<?php

namespace App\Services;

use App\Jobs\Email\CheckEmails;
use App\Jobs\Supplier\UnloadOnTime;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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

        Log::info('time: ' . $time);

        if ($time === "03") {
            Log::info('start command');
            Artisan::call('supplier:unload-on-time');
        }
    }
}
