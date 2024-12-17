<?php

namespace App\Helpers;
use App\Exceptions\ReportCancelled;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Bus\Batch;
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

    static public function toBatch(\Closure $callback, string $queue = 'default', \Closure $cancel = null, \Closure $progress = null): void
    {
        $batch = Bus::batch([])->onQueue($queue)->dispatch();

        $callback($batch);

        if (!$batch instanceof Batch) return;

        $batch = $batch->fresh();

        while (!$batch->finished() && ($batch->pendingJobs > 0)) {
            $batch = $batch->fresh();
            if ($batch->hasFailures()) throw new \Exception('Batch failed!');
            if ($cancel) {
                if ($cancel()) {
                    $batch->cancel();
                }
            }
            $batch = $batch->fresh();
            if ($batch->cancelled()) throw new ReportCancelled('Batch cancelled!');
            if ($progress) $progress($batch->progress());
            sleep(20);
        }

        if (!($batch->totalJobs > 0)) $batch->delete();
    }
}
