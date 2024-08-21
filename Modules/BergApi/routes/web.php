<?php

use Illuminate\Support\Facades\Route;
use Modules\BergApi\Http\Controllers\BergApiController;

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
    Route::get('/bergapi/{page?}', \Modules\BergApi\Livewire\BergApi\BergApiIndex::class)->name('bergapi.index');
});
