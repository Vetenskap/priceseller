<?php

namespace App\Services;

use App\HttpClient\WbClient;
use App\Models\Supplier;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class WbItemPriceService
{
    protected WbClient $wbClient;

    public function __construct(public Supplier $supplier, public WbMarket $market)
    {
        $this->wbClient = new WbClient($this->market->api_key);
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
            ->chunk(1000, function ($items) {
                $items->each(function (WbItem $wbItem) {
                    $wbItem = $this->recountPriceWbItem($wbItem);
                    $wbItem->save();
                });
            });
    }

    public function updatePriceTest(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт цен");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items) {
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
            ->chunk(1000, function ($items) {
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
        $newCount = (int)max($newCount, 0);

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

    public function nullAllStocks(): void
    {
        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->update(['count' => 0]);
    }

    public function unloadAllStocks(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка остатков в кабинет");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function (Collection $items) {
                $this->market->warehouses->map(function (WbWarehouse $warehouse) use ($items) {

                    $data = $items->map(function (WbItem $item) {
                        return [
                            "sku" => (string)$item->sku,
                            "amount" => (int)$item->count,
                        ];
                    });

                    if (App::isProduction()) {
                        $this->wbClient->putStocks($data->all(), $warehouse->id);
                    } else {
//                        Log::debug('Вб: обновление остатков', [
//                            'market' => $this->market->name,
//                            'supplier' => $this->supplier->name,
//                            'data' => $data
//                        ]);
                    }

                });
            });
    }

    public function unloadAllPrices(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка цен в кабинет");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->whereNotNull('volume')
            ->whereNotNull('retail_markup_percent')
            ->whereNotNull('package')
            ->whereNotNull('sales_percent')
            ->whereNotNull('min_price')
            ->whereNotNull('price')
            ->whereNotNull('nm_id')
            ->chunk(1000, function (Collection $items) {

                $data = $items->map(function (WbItem $item) {

                    return [
                        "nmId" => (int)$item->nm_id,
                        "price" => (int)$item->price
                    ];
                });

                if (App::isProduction()) {
                    $this->wbClient->putPrices($data->all());
                } else {
//                    Log::debug('Вб: обновление цен', [
//                        'market' => $this->market->name,
//                        'supplier' => $this->supplier->name,
//                        'data' => $data
//                    ]);
                }

            });
    }
}
