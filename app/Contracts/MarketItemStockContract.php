<?php

namespace App\Contracts;

use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\ReportLog;
use App\Models\Supplier;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Builder;

interface MarketItemStockContract
{
    public function updateStock(): void;
    public function recountStockItem(WbItem|OzonItem $item): void;
    public function recountStockBundle(WbItem|OzonItem $item): void;
    public function nullAllStocks(): void;
    public function unloadAllStocks(): void;
    public function make(Supplier $supplier, WbMarket|OzonMarket $market, ReportLog $log, array $supplierWarehousesIds): void;
    public function filteredData(Builder $query): Builder;
}
