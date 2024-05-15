<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\WbItem;
use App\Models\WbMarket;
use Illuminate\Database\Eloquent\Builder;

class WbItemPriceService
{
    public function __construct(public Supplier $supplier, public WbMarket $market)
    {
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт цен");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items){
                $items->each(function (WbItem $wbItem) {
                    $wbItem = $this->recountPriceWbItem($wbItem);
                    $wbItem->save();
                });
            });
    }

    public function recountPriceWbItem(WbItem $wbItem): WbItem
    {
        $coefficient = (float)$this->market->coefficient;
        $basicLogistics = (int)$this->market->basic_logistics;
        $priceOneLiter = (int)$this->market->price_one_liter;
        $volume = (int)$this->market->volume;

        $volumeColumn = $wbItem->volume;
        $price = $wbItem->item->price;
        $multiplicity = $wbItem->item->multiplicity;
        $retailMarkupPercent = $wbItem->retail_markup_percent / 100 + 1;
        $package = $wbItem->package;
        $salesPercent = $wbItem->sales_percent;
        $minPrice = $wbItem->min_price;

        if ($volumeColumn < $volume) {
            $liter = $basicLogistics;
        } else {
            $liter = $basicLogistics + (($volumeColumn - $volume) * $priceOneLiter);
        }

        $formula = (((($price * $multiplicity * $retailMarkupPercent) + $package + $liter) * 100 / (100 - $salesPercent))) * $coefficient;

        $newPrice = floor(max($formula, $minPrice));

        $wbItem->price = $newPrice;

        return $wbItem;
    }

    public function updateStock(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт остатков");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items){
                $items->each(function (WbItem $wbItem) {
                    $wbItem = $this->recountStockWbItem($wbItem);
                    $wbItem->save();
                });
            });

        $this->nullNotUpdatedStocks();
    }

    public function recountStockWbItem(WbItem $wbItem): WbItem
    {

        $newCount = $wbItem->item->count < $this->market->min ? 0 : $wbItem->item->count;
        $newCount = ($newCount >= $this->market->min && $newCount <= $this->market->max && $wbItem->item->multiplicity = 1) ? 1 : $newCount;
        $newCount = $newCount / $wbItem->item->multiplicity;
        $newCount = $newCount > $this->market->max_count ? $this->market->max_count : $newCount;
        $newCount = (int) max($newCount, 0);

        $wbItem->count = $newCount;

        return $wbItem;
    }

    public function nullNotUpdatedStocks(): void
    {
        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', false)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->update(['count' => 0]);
    }
}
