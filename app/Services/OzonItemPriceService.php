<?php

namespace App\Services;

use App\HttpClient\OzonClient;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class OzonItemPriceService
{
    protected OzonClient $ozonClient;

    public function __construct(public Supplier $supplier, public OzonMarket $market)
    {
        $this->ozonClient = new OzonClient($this->market->api_key, $this->market->client_id);
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт цен");

        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items) {
                $items->each(function (OzonItem $ozonItem) {
                    $ozonItem = $this->recountPriceOzonItem($ozonItem);
                    $ozonItem->save();
                });
            });
    }

    public function updatePriceTest()
    {
        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items) {
                $items->each(function (OzonItem $ozonItem) {
                    $ozonItem = $this->recountPriceOzonItem($ozonItem);
                    $ozonItem->save();
                });
            });
    }

    public function recountPriceOzonItem(OzonItem $ozonItem): OzonItem
    {
        $min_price_percent = (float)$this->market->min_price_percent;
        $seller_price_percent = (float)$this->market->seller_price_percent;
        $max_price_percent = (float)$this->market->max_price_percent / 100 + 1;
        $acquiring = (float)$this->market->acquiring;
        $lastMile = (float)$this->market->last_mile;
        $maxMile = (float)$this->market->max_mile;

        $price = $ozonItem->item->price;
        $multiplicity = $ozonItem->item->multiplicity;
        $shipping_processing = $ozonItem->shipping_processing;
        $direct_flow_trans = $ozonItem->direct_flow_trans;
        $sales_percent = $ozonItem->sales_percent;
        $min_price = $ozonItem->min_price;
        $min_price_percent_column = (float)$ozonItem->min_price_percent / 100 + 1;

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $min_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring + $min_price_percent)));
        }

        $ozonItem->price_min = floor(max($secondFormula, $min_price));

        $newFormulaOzon = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans)
            * (100 / (100 - ($sales_percent + $acquiring + $lastMile + $min_price_percent + $seller_price_percent)));

        $secondFormula = $newFormulaOzon;

        if ($newFormulaOzon * ($lastMile / 100) >= $maxMile) {
            $secondFormula = ($price * $multiplicity * $min_price_percent_column + $shipping_processing + $direct_flow_trans + $maxMile)
                * (100 / (100 - ($sales_percent + $acquiring + $min_price_percent + $seller_price_percent)));
        }

        $ozonItem->price = floor(max($secondFormula, $min_price));

        $ozonItem->price_max = floor($ozonItem->price_min * $max_price_percent);

        if ($this->market->seller_price && $ozonItem->price_seller > 0) {
            $formulaPriceSeller = $ozonItem->price_seller > $price
                ? $ozonItem->price
                : $ozonItem->price_seller - ($ozonItem->price_seller / 100);

            $ozonItem->price = floor(max($formulaPriceSeller, $ozonItem->price_min));
        }

        return $ozonItem;
    }

    public function updateStock(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт остатков");

        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->chunk(1000, function ($items) {
                $items->each(function (OzonItem $ozonItem) {
                    $ozonItem = $this->recountStockOzonItem($ozonItem);
                    $ozonItem->save();
                });
            });

        $this->nullNotUpdatedStocks();
    }

    public function recountStockOzonItem(OzonItem $ozonItem): OzonItem
    {
        $new_count = $ozonItem->item->count < $this->market->min ? 0 : $ozonItem->item->count;
        $new_count = ($new_count >= $this->market->min && $new_count <= $this->market->max && $ozonItem->item->multiplicity === 1) ? 1 : $new_count;
        $new_count = $new_count / $ozonItem->item->multiplicity;
        $new_count = $new_count > $this->market->max_count ? $this->market->max_count : $new_count;
        $new_count = (int)max($new_count, 0);

        $ozonItem->count = $new_count;

        return $ozonItem;
    }

    public function nullNotUpdatedStocks(): void
    {
        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', false)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->update(['count' => 0]);
    }

    public function nullAllStocks(): void
    {
        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->update(['count' => 0]);
    }

    public function unloadAllStocks(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: выгрузка остатков в кабинет");

        if (!$this->market->warehouses()->count()) {
            SupplierReportService::addLog($this->supplier, "Нет складов. Остатки не выгружены");
            return;
        }

        $this->market->warehouses()
            ->whereHas('suppliers', function (Builder $query) {
                $query->where('supplier_id', $this->supplier->id);
            })
            ->get()
            ->map(function (OzonWarehouse $warehouse) {

                SupplierReportService::addLog($this->supplier, "Склад {$warehouse->name}: выгрузка остатков");

                OzonItem::query()
                    ->whereHas('item', function (Builder $query) {
                        $query
                            ->where('supplier_id', $this->supplier->id);
                    })
                    ->chunk(100, function (Collection $items) use ($warehouse) {

                        $data = $items->map(function (OzonItem $item) use ($warehouse) {
                            return [
                                'offer_id' => (string)$item->offer_id,
                                'product_id' => (int)$item->product_id,
                                'stock' => (int)$item->count,
                                'warehouse_id' => (int)$warehouse->warehouse_id
                            ];
                        });

                        if (App::isProduction()) {
                            $this->ozonClient->putStocks($data->all(), $this->supplier);
                        } else {
//                        Log::debug('Озон: обновление остатков', [
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
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: цен в кабинет");

        OzonItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->where('updated', true)
                    ->where('supplier_id', $this->supplier->id);
            })
            ->whereNotNull('price_min')
            ->whereNotNull('offer_id')
            ->whereNotNull('price_max')
            ->whereNotNull('price')
            ->whereNotNull('product_id')
            ->whereNotNull('shipping_processing')
            ->whereNotNull('direct_flow_trans')
            ->whereNotNull('sales_percent')
            ->whereNotNull('min_price')
            ->whereNotNull('min_price_percent')
            ->chunk(1000, function (Collection $items) {

                $data = $items->map(function (OzonItem $item) {

                    return [
                        "auto_action_enabled" => "UNKNOWN",
                        "currency_code" => "RUB",
                        "min_price" => (string)$item->price_min,
                        "offer_id" => (string)$item->offer_id,
                        "old_price" => (string)$item->price_max,
                        "price" => (string)$item->price,
                        "product_id" => (int)$item->product_id
                    ];
                });

                if (App::isProduction()) {
                    $this->ozonClient->putPrices($data->all());
                } else {
//                    Log::debug('Озон: обновление цен', [
//                        'market' => $this->market->name,
//                        'supplier' => $this->supplier->name,
//                        'data' => $data
//                    ]);
                }
            });
    }
}
