<?php

namespace App\Helpers;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class Helpers {
    static public function getTimeZoneList()
    {
        return Cache::rememberForever('timezones_list_collection', function () {
            $timestamp = time();
            foreach (timezone_identifiers_list(\DateTimeZone::ALL) as $key => $value) {
                date_default_timezone_set($value);
                $timezone[$value] = $value . ' (UTC ' . date('P', $timestamp) . ')';
            }
            return collect($timezone)->sortKeys();
        });
    }

    static public function getUserTimeZone(?User $user = null)
    {
        return optional(static::user())->timezone ?? ($user ? $user->timezone : config('app.timezone'));
    }

    static public function currentUser(): Employee|User
    {
        if (Auth::guard('employee')->check()) {
            // Если авторизован сотрудник
            return Auth::guard('employee')->user();
        }

        return Auth::user(); // Обычный пользователь
    }

    static public function user(): User|null
    {
        if (Auth::guard('employee')->check()) {
            // Если авторизован сотрудник
            return Auth::guard('employee')->user()->user;
        }

        return Auth::user(); // Обычный пользователь
    }

    static public function toBatch(\Closure $callback, string $queue = 'default'): void
    {
        $batch = Bus::batch([])->onQueue($queue)->dispatch();

        $callback($batch);

        while (!$batch->finished()) {
            sleep(60);
            $batch = $batch->fresh();
        }
    }
}
