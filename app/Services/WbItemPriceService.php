<?php

namespace App\Services;

use App\HttpClient\WbClient;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use App\Models\WbWarehouseUserWarehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class WbItemPriceService
{
    protected WbClient $wbClient;
    protected User $user;

    public function __construct(public ?Supplier $supplier = null, public WbMarket $market)
    {
        $this->wbClient = new WbClient($this->market->api_key);
        $this->user = $this->market->user;
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт цен");

        WbItem::query()
            ->whereHas('item', function (Builder $query) {
                $query
                    ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                        $query->where('updated', true);
                    })
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
        if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$wbItem->item->price) {
            $price = $wbItem->item->buy_price_reserve;
        } else {
            $price = $wbItem->item->price;
        }

        $coefficient = (float)$this->market->coefficient;
        $basicLogistics = (int)$this->market->basic_logistics;
        $priceOneLiter = (int)$this->market->price_one_liter;
        $volume = (int)$this->market->volume;

        $volumeColumn = $wbItem->volume;
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
        $this->market->warehouses->each(function (WbWarehouse $warehouse) use ($wbItem) {
            $myWarehousesStocks = $warehouse->userWarehouses->map(function (WbWarehouseUserWarehouse $userWarehouse) use ($wbItem) {
                return $userWarehouse->warehouse->stocks()->where('item_id', $wbItem->item_id)->first()?->stock;
            })->sum();

            $new_count = $wbItem->item->count - $this->market->minus_stock;
            $new_count = $new_count < $this->market->min ? 0 : $new_count;
            $new_count = ($new_count >= $this->market->min && $new_count <= $this->market->max && $wbItem->item->multiplicity === 1) ? 1 : $new_count;
            $new_count = ($new_count + $myWarehousesStocks) / $wbItem->item->multiplicity;

            if (ModuleService::moduleIsEnabled('Order', $this->user)) {
                $new_count = $new_count - ($wbItem->orders()->where('state', 'new')->sum('count') * $wbItem->item->multiplicity);
            }

            if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                $new_count = $new_count - (($wbItem->item->moyskladOrders()->where('new', true)->exists() ? $wbItem->item->moyskladOrders()->where('new')->sum('orders') : 0) * $wbItem->item->multiplicity);
            }

            $new_count = $new_count > $this->market->max_count ? $this->market->max_count : $new_count;
            $new_count = (int)max($new_count, 0);

            $warehouse->stocks()->updateOrCreate([
                'wb_item_id' => $wbItem->id
            ], [
                'wb_item_id' => $wbItem->id,
                'stock' => $new_count
            ]);
        });

        return $wbItem;
    }

    public function nullNotUpdatedStocks(): void
    {
        WbWarehouseStock::query()
            ->whereHas('wbItem', function (Builder $query) {
                $query->whereHas('item', function (Builder $query) {
                    $query
                        ->where('updated', false)
                        ->where('supplier_id', $this->supplier->id);
                });
            })
            ->update(['stock' => 0]);
    }

    public function nullAllStocks(): void
    {
        WbWarehouseStock::query()
            ->whereHas('wbItem', function (Builder $query) {
                $query->whereHas('item', function (Builder $query) {
                    $query
                        ->where('supplier_id', $this->supplier->id);
                });
            })
            ->update(['stock' => 0]);
    }

    public function unloadWbItemStocks(WbItem $wbItem): void
    {
        $this->market->warehouses()
            ->whereHas('market', function (Builder $query) use ($wbItem) {
                $query->whereHas('items', function (Builder $query) use ($wbItem) {
                    $query->where('id', $wbItem->item_id);
                });
            })
            ->get()
            ->map(function (WbWarehouse $warehouse) use ($wbItem) {

                $data = collect([
                    [
                        "sku" => (string)$wbItem->sku,
                        "amount" => (int) $wbItem->warehouseStock($warehouse) ? $wbItem->warehouseStock($warehouse)->stock : 0,
                    ]
                ]);

                if (App::isProduction()) {
                    $this->wbClient->putStocks($data, $warehouse->warehouse_id, $this->supplier);
                }
            });
    }

    public function unloadAllStocks(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка остатков в кабинет");

        if (!$this->market->warehouses()->count()) {
            SupplierReportService::addLog($this->supplier, "Нет складов. Остатки не выгружены");
            return;
        }

        $this->market->warehouses()
            ->whereHas('suppliers', function (Builder $query) {
                $query->where('supplier_id', $this->supplier->id);
            })
            ->get()
            ->map(function (WbWarehouse $warehouse) {

                SupplierReportService::addLog($this->supplier, "Склад {$warehouse->name}: выгрузка остатков");

                WbItem::query()
                    ->whereHas('item', function (Builder $query) {
                        $query
                            ->where('unload_wb', true)
                            ->where('supplier_id', $this->supplier->id);
                    })
                    ->chunk(1000, function (Collection $items) use ($warehouse) {

                        $data = $items->map(function (WbItem $item) use ($warehouse) {
                            return [
                                "sku" => (string)$item->sku,
                                "amount" => (int) ($item->warehouseStock($warehouse) ? $item->warehouseStock($warehouse)->stock : 0),
                            ];
                        });

                        if (App::isProduction()) {
                            $this->wbClient->putStocks($data, $warehouse->warehouse_id, $this->supplier);
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
                    ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                        $query->where('updated', true);
                    })
                    ->where('supplier_id', $this->supplier->id);
            })
            ->whereNotNull('volume')
            ->whereNotNull('retail_markup_percent')
            ->whereNotNull('package')
            ->whereNotNull('sales_percent')
            ->whereNotNull('min_price')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereNotNull('nm_id')
            ->chunk(1000, function (Collection $items) {

                $data = $items->map(function (WbItem $item) {

                    return [
                        "nmId" => (int)$item->nm_id,
                        "price" => (int)$item->price
                    ];
                });

                if (App::isProduction()) {
                    $this->wbClient->putPrices($data, $this->supplier);
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
