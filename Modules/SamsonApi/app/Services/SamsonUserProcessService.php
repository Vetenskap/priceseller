<?php

namespace Modules\SamsonApi\Services;

use App\Helpers\Helpers;
use App\Models\User;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\SamsonApi\Jobs\SamsonUserProcess;

class SamsonUserProcessService
{
    public static function processUsers(): void
    {
        $time = now();

        User::chunk(10, function (Collection $users) use ($time) {
            $users->each(function (User $user) use ($time) {
                if (ModuleService::moduleIsEnabled('SamsonApi', $user) && ($user->isAdmin() || $user->isSub() || App::isLocal())) {
                    self::checkTimeAndAddJob($user, $time->timezone(Helpers::getUserTimeZone($user))->format('H:i'));
                }
            });
        });
    }

    protected static function checkTimeAndAddJob(User $user,string $time): void
    {
        if ($user->samsonApi?->times()->where('time', $time)->exists()) {
            SamsonUserProcess::dispatch($user->samsonApi);
        }
    }
}
