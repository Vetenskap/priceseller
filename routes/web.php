<?php

use App\Livewire\ItemsImportReport\ItemsImportReportShow;
use Illuminate\Support\Facades\Route;

Route::middleware(['user_main_sub', 'auth'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/emails', \App\Livewire\Email\EmailIndex::class)->name('emails');
    Route::get('/emails/{email}', \App\Livewire\Email\EmailShow::class)->name('email-show')->whereUuid('email');

    Route::get('/suppliers', \App\Livewire\Supplier\SupplierIndex::class)->name('suppliers');
    Route::get('/suppliers/{supplier}', \App\Livewire\Supplier\SupplierEdit::class)->name('supplier-edit')->whereUuid('supplier');
    Route::get('/suppliers/{supplier}/reports/{report}', \App\Livewire\SupplierReport\SupplierReportEdit::class)->name('supplier-report-edit')->whereUuid(['supplier', 'report']);

    Route::middleware(['user_ms_sub'])->group(function () {
        Route::get('/moysklad', \App\Livewire\Moysklad\MoyskladIndex::class)->name('moysklad');
    });

    Route::middleware(['user_avito_sub'])->group(function () {
        Route::get('/avito', \App\Livewire\Avito\AvitoIndex::class)->name('avito');
    });

    Route::get('/items', \App\Livewire\Item\ItemIndex::class)->name('items');
    Route::get('/items/{item}', \App\Livewire\Item\ItemEdit::class)->name('item-edit')->whereUuid('item');

    Route::get('/ozon', \App\Livewire\OzonMarket\OzonMarketIndex::class)->name('ozon');
    Route::get('/ozon/{market}', \App\Livewire\OzonMarket\OzonMarketEdit::class)->name('ozon-market-edit')->whereUuid('market');

    Route::get('/wb', \App\Livewire\WbMarket\WbMarketIndex::class)->name('wb');
    Route::get('/wb/{market}', \App\Livewire\WbMarket\WbMarketEdit::class)->name('wb-market-edit')->whereUuid('market');

    Route::get('/import/report/{report}', ItemsImportReportShow::class)->name('items-import-report-edit');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/subscribe/main', \App\Livewire\Subscribe\MainSub::class)->name('subscribe.main');
    Route::get('/subscribe/ms', \App\Livewire\Subscribe\MsSub::class)->name('subscribe.ms');
    Route::get('/subscribe/avito', \App\Livewire\Subscribe\AvitoSub::class)->name('subscribe.avito');

    Route::view('profile', 'profile')
        ->name('profile');
});

Route::permanentRedirect('/', 'dashboard');


require __DIR__.'/auth.php';
