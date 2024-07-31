<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {

    \App\Services\BusinessLogicService::usersEmailsUnload();
    \App\Services\ReportService::checkTimeouts();

})->everyMinute()->name('everyMinuteSchedule')->withoutOverlapping();

Schedule::call(function () {

    \Illuminate\Support\Facades\Artisan::call('horizon:snapshot');
    \App\Services\UsersPermissionsService::closeMarkets();

})->everyFiveMinutes()->name('everyFiveMinutesSchedule')->withoutOverlapping();

Schedule::call(function () {

    \Illuminate\Support\Facades\Artisan::call('telescope:prune');
    \App\Services\PruneService::reports();
    \App\Services\UsersPermissionsService::sendNotifications();

})->daily();
