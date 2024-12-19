<?php

namespace Modules\VoshodApi\Services;

use App\Helpers\Helpers;
use App\Models\User;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\VoshodApi\Jobs\VoshodUserProcess;

class VoshodUserProcessService
{
   public static function processUsers(): void
   {
       $time = now();

       User::chunk(10, function (Collection $users) use ($time) {
            $users->each(function (User $user) use ($time) {
                if (ModuleService::moduleIsEnabled('VoshodApi', $user) && ($user->isAdmin() || $user->isSub() || App::isLocal())) {
                    self::checkTimeAndAddJob($user, $time->timezone(Helpers::getUserTimeZone($user))->format('H:i'));
                }
            });
       });
   }

    protected static function checkTimeAndAddJob(User $user,string $time): void
    {
        if ($user->voshodApi?->times()->where('time', $time)->exists()) {
            VoshodUserProcess::dispatch($user->voshodApi);
        }
    }

}
