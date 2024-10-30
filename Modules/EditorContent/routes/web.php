<?php

use Illuminate\Support\Facades\Route;
use Modules\EditorContent\Http\Controllers\EditorContentController;

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
    Route::get('/editor_content/{page?}', \Modules\EditorContent\Livewire\EditorContent\EditorContentIndex::class)->name('editor_content.index');
});
