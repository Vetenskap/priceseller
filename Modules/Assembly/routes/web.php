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
    Route::get('/assembly', \Modules\Assembly\Livewire\Assembly\AssemblyIndex::class)->name('assembly.index');
    Route::get('/assembly/ozon/{warehouse}', \Modules\Assembly\Livewire\Assembly\AssemblyOzon::class)->name('assembly.ozon');
    Route::get('/assembly/wb/{warehouse}', \Modules\Assembly\Livewire\Assembly\AssemblyWb::class)->name('assembly.wb');
});
