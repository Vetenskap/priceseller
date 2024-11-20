<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::prefix('modules')->group(function () {
    Route::post('orders/ozon/webhooks/{webhook}', [\Modules\Order\Http\Controllers\OrderOzonWebhookController::class, 'index'])->name('orders.ozon.webhooks')->whereUuid('webhook');
});
