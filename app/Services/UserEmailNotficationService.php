<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\SubscriptionExpires;
use App\Notifications\UserNotification;

class UserEmailNotficationService
{

    public function __construct(public User $user)
    {
    }

    public function subscribesExpires(): void
    {
        $permissions = $this->user->permissions()->wherePivot('expires', '>', now())->wherePivot('expires', '<', now()->addDays(3))->get();
        $permissions->each(function (Permission $permission) {
            $this->user->notify(new UserNotification('Ваша подписка истекает', 'Ваша подписка: [' . $permission->name . '] истекает через 3 дня. Успейте продлить'));
        });
    }
}
