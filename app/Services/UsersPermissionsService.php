<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UsersPermissionsService
{
    public static function closeMarkets(): void
    {
        User::chunk(5, function (Collection $users) {
            $users->each(function (User $user) {
                OzonMarketService::closeMarkets($user);
                WbMarketService::closeMarkets($user);
            });
        });
    }

    public static function sendNotifications(): void
    {
        User::whereNotNull('email_verified_at')->chunk(5, function (Collection $users) {
            $users->each(function (User $user) {
                $service = new UserEmailNotficationService($user);
                $service->subscribesExpires();
            });
        });
    }
}
