<?php

use App\Http\Controllers\Api\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/organizations', [OrganizationController::class, 'index'])->name('api.organizations.index');
    Route::get('/organizations/{organization}', [OrganizationController::class, 'show'])->name('api.organizations.show');

    Route::get('/warehouses', [\App\Http\Controllers\Api\WarehouseController::class, 'index'])->name('api.warehouses.index');

    Route::get('/ozon', [\App\Http\Controllers\Api\OzonMarketController::class, 'index'])->name('api.ozon.index');

    Route::get('/wb', [\App\Http\Controllers\Api\WbMarketController::class, 'index'])->name('api.wb.index');

    Route::post('/ozon/{market}/items', [\App\Http\Controllers\Api\OzonMarketController::class, 'getItem'])->name('api.ozon.items.get');
    Route::post('/wb/{market}/items', [\App\Http\Controllers\Api\WbMarketController::class, 'getItem'])->name('api.wb.items.get');
});
