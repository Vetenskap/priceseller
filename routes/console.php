<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \App\Services\ItemsExportReportService::timeout();
    \App\Services\ItemsImportReportService::timeout();
    \App\Services\SupplierReportService::timeout();
})->everyMinute();

Schedule::command('user:process')->everyMinute();

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::call(function () {
    \App\Models\User::chunk(5, function (\Illuminate\Support\Collection $users) {
        $users->each(function (\App\Models\User $user) {
            \App\Services\OzonMarketService::closeMarkets($user);
            \App\Services\WbMarketService::closeMarkets($user);
        });
    });
})->everyFiveMinutes();

Schedule::command('telescope:prune')->daily();

Schedule::call(function () {
    \App\Services\ItemsExportReportService::prune();
    \App\Services\ItemsImportReportService::prune();
    \App\Services\SupplierReportService::prune();
})->daily();
