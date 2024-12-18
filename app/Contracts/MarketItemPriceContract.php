<?php

namespace App\Contracts;

use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\ReportLog;
use App\Models\Supplier;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Builder;

interface MarketItemPriceContract
{
    public function updatePrice(): void;
    public function recountPrice(WbItem|OzonItem $item): void;
    public function unloadAllPrices(): void;
    public function make(Supplier $supplier, WbMarket|OzonMarket $market, ReportLog $log): void;
    public function getPriceAndMultiplicity(OzonItem|WbItem $item): array;
    public function applyMarketSpecificPricing(WbItem|OzonItem $item, float $price, int $multiplicity): void;
    public function calculateWbPrice(WbItem $item, float $price, int $multiplicity): float;
    public function calculateOzonPrices(OzonItem $item, float $price, int $multiplicity): array;
    public function filteredData(Builder $query): Builder;

}
