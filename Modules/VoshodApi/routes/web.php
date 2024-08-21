<?php

use Illuminate\Support\Facades\Route;
use Modules\VoshodApi\Http\Controllers\VoshodApiController;

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
    Route::get('/voshodapi/{page?}', \Modules\VoshodApi\Livewire\VoshodApi\VoshodApiIndex::class)->name('voshodapi.index');
});
