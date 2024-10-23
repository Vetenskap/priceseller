<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\HttpClient\WbClient;
use App\Jobs\Market\NullNotUpdatedStocksBatch;
use App\Jobs\Market\UpdateStockBatch;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\ItemWarehouseStock;
use App\Models\Supplier;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use App\Models\WbWarehouseSupplier;
use App\Models\WbWarehouseSupplierWarehouse;
use App\Models\WbWarehouseUserWarehouse;
use Illuminate\Bus\Batch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Modules\Moysklad\Services\MoyskladItemOrderService;

class WbItemPriceService
{
    protected User $user;

    public function __construct(public ?Supplier $supplier = null, public WbMarket $market, public array $supplierWarehousesIds)
    {
        $this->user = $this->market->user;
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт цен");

        $this->market
            ->items()
            ->with('wbitemable')
            ->chunk(1000, function (Collection $items) {

                $items->filter(function (WbItem $wbItem) {

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->wbitemable->supplier_id === $this->supplier->id) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->wbitemable->updated) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    } else {
                        if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->updated)) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    }

                    return false;

                })->each(function (WbItem $wbItem) {
                    $wbItem = $this->recountPriceWbItem($wbItem);
                    $wbItem->save();
                });

            });
    }

    public function recountPriceWbItem(WbItem $wbItem): WbItem
    {
        if ($wbItem->wbitemable_type === 'App\Models\Item') {

            $multiplicity = $wbItem->wbitemable->multiplicity;

            if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$wbItem->wbitemable->price) {
                $price = $wbItem->wbitemable->buy_price_reserve;
            } else {
                $price = $wbItem->wbitemable->price;
            }

        } else {

            $multiplicity = 1;

            $price = $wbItem->wbitemable->items->map(function (Item $item) {
                if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$item->price) {
                    return $item->buy_price_reserve * $item->pivot->multiplicity;
                } else {
                    return $item->price * $item->pivot->multiplicity;
                }
            })->sum();
        }

        $coefficient = (float)$this->market->coefficient;
        $basicLogistics = (int)$this->market->basic_logistics;
        $priceOneLiter = (int)$this->market->price_one_liter;
        $volume = (int)$this->market->volume;

        $volumeColumn = $wbItem->volume;
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

        Helpers::toBatch(function (Batch $batch) {
            $this->market
                ->items()
                ->with('wbitemable')
                ->chunk(1000, function (Collection $items) use ($batch) {
                    $batch->add(new UpdateStockBatch($this, $items));
                });
        }, 'market-update-stock');

        $this->nullNotUpdatedStocks();
    }

    public function recountStockWbItem(WbItem $wbItem): WbItem
    {
        $this->market->warehouses->each(function (WbWarehouse $warehouse) use ($wbItem) {

            /** @var WbWarehouseSupplier $wbWarehouseSupplier */
            $wbWarehouseSupplier = $warehouse->suppliers()
                ->where('supplier_id', $this->supplier->id)
                ->first();

            if (!$wbWarehouseSupplier) return;

            $supplierWarehousesIds = $wbWarehouseSupplier->warehouses()
                ->whereIn('supplier_warehouse_id', $this->supplierWarehousesIds)
                ->get()
                ->map(function (WbWarehouseSupplierWarehouse $warehouse) {
                    return $warehouse->supplier_warehouse_id;
                });

            $new_count = 0;

            if ($wbItem->wbitemable_type === 'App\Models\Item') {
                $unload_wb = !$wbItem->wbitemable->unload_wb;
            } else {
                $unload_wb = boolval($wbItem->wbitemable->items->first(fn(Item $item) => !$item->unload_wb));
            }

            if (!$unload_wb) {

                $itemIds = $wbItem->wbitemable_type === 'App\Models\Item' ? [$wbItem->wbitemable_id] : $wbItem->wbitemable->items->pluck('id')->toArray();

                $myWarehousesStocks = $warehouse->userWarehouses->map(function (WbWarehouseUserWarehouse $userWarehouse) use ($wbItem, $itemIds) {
                    return $userWarehouse->warehouse->stocks()->whereIn('item_id', $itemIds)->get()->map(fn(ItemWarehouseStock $stock) => $stock->stock)->sum();
                })->sum();

                if ($wbItem->wbitemable_type === 'App\Models\Item') {
                    $new_count = $wbItem->wbitemable->supplierWarehouseStocks()->whereIn('supplier_warehouse_id', $supplierWarehousesIds)->sum('stock');
                    $multiplicity = $wbItem->wbitemable->multiplicity;
                } else {
                    $new_count = $wbItem->wbitemable->items->map(function (Item $item) use ($supplierWarehousesIds) {

                        $count = $item->supplierWarehouseStocks()->whereIn('supplier_warehouse_id', $supplierWarehousesIds)->sum('stock') / $item->pivot->multiplicity;

                        if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                            $count = $count - (($item->moyskladOrders()->where('new', true)->exists() ? MoyskladItemOrderService::getOrders($item)->sum('orders') : 0));
                        }

                        return $count;

                    })->min();
                    $multiplicity = 1;
                }

                $new_count = $new_count - $this->market->minus_stock;
                $new_count = $new_count < $this->market->min ? 0 : $new_count;
                $new_count = ($new_count >= $this->market->min && $new_count <= $this->market->max && $multiplicity === 1) ? 1 : $new_count;
                $new_count = ($new_count + $myWarehousesStocks) / $multiplicity;

                if (ModuleService::moduleIsEnabled('Order', $this->user)) {
                    $new_count = $new_count - ($wbItem->orders()->where('state', 'new')->sum('count') * $multiplicity);
                }

                if ($wbItem->wbitemable_type === 'App\Models\Item') {
                    if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                        $new_count = $new_count - (($wbItem->wbitemable->moyskladOrders()->where('new', true)->exists() ? MoyskladItemOrderService::getOrders($wbItem->wbitemable)->sum('orders') : 0) * $wbItem->wbitemable->multiplicity);
                    }
                }

                $new_count = $new_count > $this->market->max_count ? $this->market->max_count : $new_count;
                $new_count = (int)max($new_count, 0);

            }

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
        Helpers::toBatch(function (Batch $batch) {
            WbWarehouseStock::query()
                ->with('wbItem')
                ->whereHas('wbItem', function (Builder $query) {
                    $query->where('wb_market_id', $this->market->id);
                })
                ->whereHas('warehouse', function (Builder $query) {
                    $query->whereHas('suppliers', function (Builder $query) {
                        $query
                            ->where('supplier_id', $this->supplier->id)
                            ->whereHas('warehouses', function (Builder $query) {
                                $query->whereIn('supplier_warehouse_id', $this->supplierWarehousesIds);
                            });
                    });
                })
                ->chunk(1000, function (Collection $stocks) use ($batch) {

                    $batch->add(new NullNotUpdatedStocksBatch($this, $stocks));

                });
        }, 'market-update-stock');

    }

    public function nullAllStocks(): void
    {
        WbWarehouseStock::query()
            ->with('wbItem')
            ->whereHas('wbItem', function (Builder $query) {
                $query->where('wb_market_id', $this->market->id);
            })
            ->chunk(1000, function (Collection $stocks) {

                $stocks->filter(function (WbWarehouseStock $stock) {

                    $wbItem = $stock->wbItem;

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->wbitemable->supplier_id === $this->supplier->id) {
                            return true;
                        }
                    } else {
                        if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            return true;
                        }
                    }

                    return false;

                })->each(function (WbWarehouseStock $stock) {
                    $stock->update(['stock' => 0]);
                });

            });
    }

//    public function unloadWbItemStocks(WbItem $wbItem): void
//    {
//        $this->market->warehouses()
//            ->whereHas('market', function (Builder $query) use ($wbItem) {
//                $query->whereHas('items', function (Builder $query) use ($wbItem) {
//                    $query->where('id', $wbItem->item_id);
//                });
//            })
//            ->get()
//            ->map(function (WbWarehouse $warehouse) use ($wbItem) {
//
//                $data = collect([
//                    [
//                        "sku" => (string)$wbItem->sku,
//                        "amount" => (int)$wbItem->warehouseStock($warehouse) ? $wbItem->warehouseStock($warehouse)->stock : 0,
//                    ]
//                ]);
//
//                if (App::isProduction()) {
//                    $this->wbClient->putStocks($data, $warehouse->warehouse_id, $this->supplier);
//                }
//            });
//    }

    public function unloadAllStocks(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка остатков в кабинет");

        if (!$this->market->warehouses()->count()) {
            SupplierReportService::addLog($this->supplier, "Нет складов. Остатки не выгружены");
            return;
        }

        $this->market->warehouses()
            ->whereHas('suppliers', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id)
                    ->whereHas('warehouses', function (Builder $query) {
                        $query->where('supplier_warehouse_id', $this->supplierWarehousesIds);
                    });
            })
            ->get()
            ->map(function (WbWarehouse $warehouse) {

                SupplierReportService::addLog($this->supplier, "Склад {$warehouse->name}: выгрузка остатков");

                $this->market
                    ->items()
                    ->with('wbitemable')
                    ->chunk(1000, function (Collection $items) use ($warehouse) {

                        $data = $items->filter(function (WbItem $wbItem) {

                            if ($wbItem->wbitemable_type === Item::class) {
                                if ($wbItem->wbitemable->supplier_id === $this->supplier->id) {
                                    return true;
                                }
                            } else {
                                if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                                    return true;
                                }
                            }

                            return false;

                        })->map(function (WbItem $item) use ($warehouse) {
                            return [
                                "sku" => (string)$item->sku,
                                "amount" => (int)($item->warehouseStock($warehouse) ? $item->warehouseStock($warehouse)->stock : 0),
                            ];
                        });

                        if (App::isProduction()) {
                            $wbClient = new WbClient($this->market->api_key);
                            $wbClient->putStocks($data->values()->all(), $warehouse->warehouse_id, $this->supplier);
                        }

                    });
            });
    }

    public function unloadAllPrices(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка цен в кабинет");

        $this->market
            ->items()
            ->with('wbitemable')
            ->whereNotNull('volume')
            ->whereNotNull('retail_markup_percent')
            ->whereNotNull('package')
            ->whereNotNull('sales_percent')
            ->whereNotNull('min_price')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereNotNull('nm_id')
            ->chunk(1000, function (Collection $items) {

                $data = $items->filter(function (WbItem $wbItem) {

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->wbitemable->supplier_id === $this->supplier->id) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->wbitemable->updated) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    } else {
                        if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->wbitemable->items->every(fn(Item $item) => $item->updated)) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    }

                    return false;

                })->map(function (WbItem $item) {

                    return [
                        "nmId" => (int)$item->nm_id,
                        "price" => (int)$item->price
                    ];
                });

                if (App::isProduction()) {
                    $wbClient = new WbClient($this->market->api_key);
                    $wbClient->putPrices($data->values()->all(), $this->supplier);
                }

            });
    }
}
