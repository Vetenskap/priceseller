<?php

use Illuminate\Support\Facades\Route;
use Modules\SimalandApi\Http\Controllers\SimalandApiController;

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

Route::group([], function () {
    Route::resource('simalandapi', SimalandApiController::class)->names('simalandapi');
});
