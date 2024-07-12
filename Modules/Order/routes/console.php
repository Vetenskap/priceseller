<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \Modules\Order\Services\OrderService::prune();
})->daily();
