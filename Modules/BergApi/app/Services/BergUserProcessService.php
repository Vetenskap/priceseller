<?php

namespace Modules\BergApi\Services;

use App\Helpers\Helpers;
use App\Models\User;
use App\Services\ModuleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\BergApi\Jobs\BergUserProcess;

class BergUserProcessService
{
    public static function processUsers(): void
    {
        $time = now();

        User::chunk(10, function (Collection $users) use ($time) {
            $users->each(function (User $user) use ($time) {
                if (ModuleService::moduleIsEnabled('BergApi', $user) && ($user->isAdmin() || $user->isSub() || App::isLocal())) {
                    self::checkTimeAndAddJob($user, $time->timezone(Helpers::getUserTimeZone($user))->format('H:i'));
                }
            });
        });
    }

    protected static function checkTimeAndAddJob(User $user,string $time): void
    {
        if ($user->bergApi?->times()->where('time', $time)->exists()) {
            BergUserProcess::dispatch($user->bergApi);
        }
    }
}
