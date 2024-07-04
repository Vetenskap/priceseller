<?php

use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    \Modules\Order\Models\Order::where('created_at', '<', now()->subMonth())->delete();
})->daily();
