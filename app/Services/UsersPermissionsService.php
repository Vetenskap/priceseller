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
                if (!$user->isAdmin()) {
                    OzonMarketService::closeMarkets($user);
                    WbMarketService::closeMarkets($user);
                }
            });
        });
    }

    public static function sendNotifications(): void
    {
        User::whereNotNull('email_verified_at')->chunk(5, function (Collection $users) {
            $users->each(function (User $user) {
                if (!$user->isAdmin()) {
                    $service = new UserEmailNotficationService($user);
                    $service->subscribesExpires();
                }
            });
        });
    }

    public static function getExpiringSubscribes(User $user): Collection
    {
        return $user->permissions()->wherePivot('expires', '>', now())->wherePivot('expires', '<', now()->addDays(3))->get();
    }
}
