<?php

use App\Livewire\ItemsImportReport\ItemsImportReportShow;
use App\Livewire\Module\ModuleIndex;
use App\Livewire\Warehouse\WarehouseEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::get('/settings', \App\Livewire\BaseSettings\BaseSettingsIndex::class)->name('base-settings.index');

    Route::get('/organizations', \App\Livewire\Organization\OrganizationIndex::class)->name('organizations.index');
    Route::get('/organizations/{organization}', \App\Livewire\Organization\OrganizationEdit::class)->name('organizations.edit')->whereUuid('organization');

    Route::get('/warehouses/{page?}', \App\Livewire\Warehouse\WarehouseIndex::class)->name('warehouses.index');
    Route::get('/warehouses/list/{warehouse}', WarehouseEdit::class)->name('warehouses.edit')->whereUuid('warehouse');

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/emails', \App\Livewire\Email\EmailIndex::class)->name('emails.index');
    Route::get('/emails/{email}', \App\Livewire\Email\EmailEdit::class)->name('email.edit')->whereUuid('email');

    Route::get('/suppliers', \App\Livewire\Supplier\SupplierIndex::class)->name('suppliers.index');
    Route::get('/suppliers/{supplier}/{page?}', \App\Livewire\Supplier\SupplierEdit::class)->name('supplier.edit')->whereUuid('supplier');
    Route::get('/suppliers/{supplier}/reports/{report}', \App\Livewire\SupplierReport\SupplierReportEdit::class)->name('supplier.report.edit')->whereUuid(['supplier', 'report']);

    Route::middleware(['user_ms_sub'])->group(function () {
        Route::get('/moysklad', \App\Livewire\Moysklad\MoyskladIndex::class)->name('moysklad');
    });

    Route::middleware(['user_avito_sub'])->group(function () {
        Route::get('/avito', \App\Livewire\Avito\AvitoIndex::class)->name('avito');
    });

    Route::get('/items/{page?}', \App\Livewire\Item\ItemIndex::class)->name('items');
    Route::get('/items/list/{item}', \App\Livewire\Item\ItemEdit::class)->name('item-edit')->whereUuid('item');

    Route::get('/bundles/{page?}', \App\Livewire\Bundle\BundleIndex::class)->name('bundles.index');
    Route::get('/bundles/list/{bundle}', \App\Livewire\Item\ItemEdit::class)->name('bundles.edit')->whereUuid('bundle');


    Route::get('/ozon', \App\Livewire\OzonMarket\OzonMarketIndex::class)->name('ozon');
    Route::get('/ozon/{market}/{page?}', \App\Livewire\OzonMarket\OzonMarketEdit::class)->name('ozon-market-edit')->whereUuid('market');

    Route::get('/wb', \App\Livewire\WbMarket\WbMarketIndex::class)->name('wb');
    Route::get('/wb/{market}/{page?}', \App\Livewire\WbMarket\WbMarketEdit::class)->name('wb-market-edit')->whereUuid('market');

    Route::get('/import/report/{report}', ItemsImportReportShow::class)->name('items-import-report-edit');

    Route::get('/modules', ModuleIndex::class)->name('modules.index');

});

Route::get('/privacy-policy', function () {
    return response()->file(\Illuminate\Support\Facades\Storage::path('main/privacy-policy.pdf'));
})->name('privacy-policy');

Route::get('/cookies', function () {
    return response()->file(\Illuminate\Support\Facades\Storage::path('main/cookies.pdf'));
})->name('cookies');

Route::middleware(['auth'])->group(function () {

    Route::view('profile', 'profile')
        ->name('profile');
});

Route::permanentRedirect('/', 'dashboard');


require __DIR__ . '/auth.php';
