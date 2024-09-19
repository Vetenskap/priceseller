<?php

use Illuminate\Support\Facades\Route;
use Modules\Assembly\Http\Controllers\AssemblyController;

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
    Route::get('/assembly/{page?}', \Modules\Assembly\Livewire\Assembly\AssemblyIndex::class)->name('assembly.index');
});
