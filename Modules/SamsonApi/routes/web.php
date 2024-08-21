<?php

use Illuminate\Support\Facades\Route;
use Modules\SamsonApi\Http\Controllers\SamsonApiController;

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
    Route::get('/samsonapi/{page?}', \Modules\SamsonApi\Livewire\SamsonApi\SamsonApiIndex::class)->name('samsonapi.index');
});
