<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use App\Notifications\SubscriptionExpires;

class UserEmailNotficationService
{

    public function __construct(public User $user)
    {
    }

    public function subscribesExpires(): void
    {
        $permissions = $this->user->permissions()->withPivot('expires')->wherePivot('expires', '>', now())->wherePivot('expires', '<', now()->addDays(3))->get();
        $permissions->each(function (Permission $permission) {
            $this->user->notify(new SubscriptionExpires($permission));
        });
    }
}
