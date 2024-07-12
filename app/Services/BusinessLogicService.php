<?php

namespace App\Services;

use App\Jobs\Email\CheckEmails;
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
                    CheckEmails::dispatchIf($user->isAdmin(), $user->id);
                } else {
                    CheckEmails::dispatchIf($user->isSub(), $user->id);
                }
            });
        });
    }
}
