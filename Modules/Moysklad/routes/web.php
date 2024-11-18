<?php

use Illuminate\Support\Facades\Route;
use Modules\Moysklad\Http\Controllers\MoyskladController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('modules')->group( function () {
    Route::get('/moysklad/{page?}', \Modules\Moysklad\Livewire\Moysklad\MoyskladIndex::class)->name('moysklad.index');
    Route::get('/moysklad/items/report/{report}', \Modules\Moysklad\Livewire\MoyskladItemReport\MoyskladItemReportShow::class)->name('moysklad.item.reports.show')->whereUuid('report');
    Route::get('/moysklad/bundles/report/{report}', \Modules\Moysklad\Livewire\MoyskladBundleApiReport\MoyskladBundleApiReportShow::class)->name('moysklad.bundle.reports.show')->whereUuid('report');
});
