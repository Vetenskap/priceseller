<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \App\Services\ItemsExportReportService::timeout();
    \App\Services\ItemsImportReportService::timeout();
    \App\Services\SupplierReportService::timeout();
})->everyMinute();

Schedule::command('user:process')->everyMinute();

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::command('telescope:prune')->daily();

Schedule::call(function () {
    \App\Services\ItemsExportReportService::prune();
    \App\Services\ItemsImportReportService::prune();
    \App\Services\SupplierReportService::prune();
})->daily();
