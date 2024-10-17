<?php

use App\Livewire\ItemsImportReport\ItemsImportReportShow;
use App\Livewire\Module\ModuleIndex;
use App\Livewire\Warehouse\WarehouseEdit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['dynamic.auth:web,employee'])->group(function () {

    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/settings', \App\Livewire\BaseSettings\BaseSettingsIndex::class)->name('base-settings.index');

    Route::get('/organizations', \App\Livewire\Organization\OrganizationIndex::class)->name('organizations.index');
    Route::get('/organizations/{organization}', \App\Livewire\Organization\OrganizationEdit::class)->name('organizations.edit')->whereUuid('organization');

    Route::get('/warehouses/{page?}', \App\Livewire\Warehouse\WarehouseIndex::class)->name('warehouses.index');
    Route::get('/warehouses/list/{warehouse}', WarehouseEdit::class)->name('warehouses.edit')->whereUuid('warehouse');

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

    Route::get('/items', \App\Livewire\Item\ItemIndex::class)->name('items');
    Route::get('/items/{item}', \App\Livewire\Item\ItemEdit::class)->name('item-edit')->whereUuid('item');

    Route::get('/bundles', \App\Livewire\Bundle\BundleIndex::class)->name('bundles.index');
    Route::get('/bundles/{bundle}', \App\Livewire\Bundle\BundleEdit::class)->name('bundles.edit')->whereUuid('bundle');


    Route::get('/ozon', \App\Livewire\OzonMarket\OzonMarketIndex::class)->name('ozon');
    Route::get('/ozon/{market}', \App\Livewire\OzonMarket\OzonMarketEdit::class)->name('ozon-market-edit')->whereUuid('market');
    Route::get('/ozon/items/{item}', \App\Livewire\OzonItem\OzonItemEdit::class)->name('ozon.item.edit')->whereUuid('item');

    Route::get('/wb', \App\Livewire\WbMarket\WbMarketIndex::class)->name('wb');
    Route::get('/wb/{market}', \App\Livewire\WbMarket\WbMarketEdit::class)->name('wb-market-edit')->whereUuid('market');
    Route::get('/wb/items/{item}', \App\Livewire\WbItem\WbItemEdit::class)->name('wb.item.edit')->whereUuid('item');

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
