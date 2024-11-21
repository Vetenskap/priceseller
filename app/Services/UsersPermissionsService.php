<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class UsersPermissionsService
{
    public static function sendNotifications(): void
    {
        User::chunk(5, function (Collection $users) {
            $users->filter(fn(User $user) => $user->userNotification()->exists() && $user->userNotification->enabled_telegram && !$user->isAdmin())->each(function (User $user) {
                $service = new UserEmailNotficationService($user);
                $service->subscribesExpires();
            });
        });
    }

    public static function getExpiringSubscribes(User $user): Collection
    {
        return $user->permissions()->wherePivot('expires', '>', now())->wherePivot('expires', '<', now()->addDays(3))->get();
    }
}
