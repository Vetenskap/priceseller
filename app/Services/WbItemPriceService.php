<?php

namespace App\Services;

use App\Helpers\Helpers;
use App\HttpClient\WbClient\WbClient;
use App\Jobs\Market\NullNotUpdatedStocksBatch;
use App\Jobs\Market\UpdateStockBatch;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
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
use Illuminate\Support\Facades\DB;
use Modules\Moysklad\Services\MoyskladItemOrderService;

class WbItemPriceService
{
    protected User $user;

    public bool $enabledOrderModule;
    public bool $enabledMoyskladModule;

    public function __construct(public ?Supplier $supplier = null, public WbMarket $market, public array $supplierWarehousesIds = [])
    {
        $this->user = $this->market->user;
        $this->market = $this->market->load([
            'warehouses',
            'warehouses.suppliers.warehouses',
            'warehouses.userWarehouses.warehouse.stocks'
        ]);
        $this->enabledMoyskladModule = ModuleService::moduleIsEnabled('Order', $this->user);
        $this->enabledOrderModule = ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders;
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: перерасчёт цен");
        $this->setLastPrices();

        $this->market
            ->items()
            ->with('itemable')
            ->chunk(10000, function (Collection $items) {

                $items->filter(function (WbItem $wbItem) {

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->itemable->supplier_id === $this->supplier->id) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->itemable->updated) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    } else {
                        if ($wbItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->itemable->items->every(fn(Item $item) => $item->updated)) {
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

            $multiplicity = $wbItem->itemable->multiplicity;

            if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$wbItem->itemable->price) {
                $price = $wbItem->itemable->buy_price_reserve;
            } else {
                $price = $wbItem->itemable->price;
            }

        } else {

            $multiplicity = 1;

            $price = $wbItem->itemable->items->map(function (Item $item) {
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

    public function setLastPrices(): void
    {
        $this->market
            ->items()
            ->where('price', '>', 0)
            ->with('itemable')
            ->chunk(10000, function (Collection $items) {

                $items->filter(function (WbItem $wbItem) {

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->itemable->supplier_id === $this->supplier->id) return true;
                    } else {
                        if ($wbItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) return true;
                    }

                    return false;

                })->each(function (WbItem $wbItem) {
                    $wbItem->update(['last_price' => DB::raw('price')]);
                });

            });
    }

    /**
     * @throws \Exception
     */
    public function updateStock(): void
    {
        $this->market
            ->items()
            ->whereHasMorph('itemable', [Item::class], function (Builder $query) {
                $query->where('supplier_id', $this->supplier->id);
            })
            ->with(['itemable.warehousesStocks', 'itemable.supplierWarehouseStocks', 'itemable.moyskladOrders'])
            ->lazy()
            ->each(function (WbItem $item) {
                $this->recountStockWbItem($item);
            });

        $this->market
            ->items()
            ->whereHasMorph('itemable', [Bundle::class], function (Builder $query) {
                $query->whereHas('items', function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                });
            })
            ->with(['itemable.items.warehousesStocks', 'itemable.items.supplierWarehouseStocks', 'itemable.items.moyskladOrders'])
            ->lazy()
            ->each(function (WbItem $item) {
                $this->recountStockWbItem($item);
            });

        $this->nullNotUpdatedStocks();
    }

    public function recountStockWbItem(WbItem $wbItem): void
    {
        if ($wbItem->wbitemable_type === Item::class) {
            $this->recountStockItem($wbItem);
        } else {
            $this->recountStockBundle($wbItem);
        }
    }

    public function recountStockItem(WbItem $wbItem)
    {
        $this->market->warehouses->each(function (WbWarehouse $warehouse) use ($wbItem) {

            /** @var WbWarehouseSupplier $wbWarehouseSupplier */
            $wbWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$wbWarehouseSupplier) return;

            if ($wbItem->itemable->unload_ozon) {

                $supplierWarehousesIds = $wbWarehouseSupplier->warehouses
                    ->filter(fn(WbWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $wbItem->itemable
                    ->warehousesStocks
                    ->filter(fn (ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                    ->sum('stock');

                $newCount = $wbItem->itemable
                    ->supplierWarehouseStocks
                    ->filter(fn (ItemSupplierWarehouseStock $stock) => in_array($stock->supplier_warehouse_id, $supplierWarehousesIds))
                    ->sum('stock');
                $multiplicity = $wbItem->itemable->multiplicity;

                $newCount = $newCount - $this->market->minus_stock;
                $newCount = $newCount < $this->market->min ? 0 : $newCount;
                $newCount = ($newCount >= $this->market->min && $newCount <= $this->market->max && $multiplicity === 1) ? 1 : $newCount;
                $newCount = ($newCount + $myWarehousesStocks) / $multiplicity;

                if ($this->market->enabled_orders) {

                    if ($this->enabledOrderModule) {
                        $newCount = $newCount - ($wbItem->orders()->where('state', 'new')->sum('count') * $multiplicity);
                    }

                    if ($wbItem->ozonitemable_type === 'App\Models\Item') {
                        if ($this->enabledMoyskladModule) {
                            $newCount = $newCount - (($wbItem->itemable->moyskladOrders->firstWhere('new', true) ? MoyskladItemOrderService::getOrders($wbItem->itemable)->sum('orders') : 0) * $wbItem->itemable->multiplicity);
                        }
                    }

                }

                $newCount = $newCount > $this->market->max_count ? $this->market->max_count : $newCount;
                $newCount = (int)max($newCount, 0);

            } else {
                $newCount = 0;
            }

            $warehouse->stocks()->updateOrCreate(
                ['wb_item_id' => $wbItem->id],
                ['stock' => $newCount]
            );
        });
    }

    public function recountStockBundle(WbItem $wbItem)
    {
        $this->market->warehouses->each(function (WbWarehouse $warehouse) use ($wbItem) {

            /** @var WbWarehouseSupplier $wbWarehouseSupplier */
            $wbWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$wbWarehouseSupplier) return;

            if (!boolval($wbItem->itemable->items->firstWhere('unload_wb', false))) {

                $supplierWarehousesIds = $wbWarehouseSupplier->warehouses
                    ->filter(fn(WbWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $wbItem->itemable
                    ->items
                    ->sum(function (Item $item) use ($warehouse) {
                        return $item
                            ->warehousesStocks
                            ->filter(fn (ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                            ->sum(fn (ItemWarehouseStock $stock) => $stock->stock / $item->pivot->multiplicity);
                    });

                $newCount = $wbItem->itemable
                    ->items
                    ->map(function (Item $item) use ($supplierWarehousesIds) {
                        $count = $item
                            ->supplierWarehouseStocks
                            ->filter(fn (ItemSupplierWarehouseStock $stock) => in_array($stock->supplier_warehouse_id, $supplierWarehousesIds))
                            ->sum(fn (ItemSupplierWarehouseStock $stock) => $stock->stock / $item->pivot->multiplicity);

                        if ($this->enabledMoyskladModule) {
                            $count = $count - (($item->moyskladOrders()->where('new', true)->exists() ? MoyskladItemOrderService::getOrders($item)->sum('orders') : 0));
                        }

                        return $count;
                    })
                    ->min();

                $multiplicity = 1;

                $newCount = $newCount - $this->market->minus_stock;
                $newCount = $newCount < $this->market->min ? 0 : $newCount;
                $newCount = ($newCount >= $this->market->min && $newCount <= $this->market->max && $multiplicity === 1) ? 1 : $newCount;
                $newCount = ($newCount + $myWarehousesStocks) / $multiplicity;

                $newCount = $newCount > $this->market->max_count ? $this->market->max_count : $newCount;
                $newCount = (int)max($newCount, 0);

            } else {
                $newCount = 0;
            }

            $warehouse->stocks()->updateOrCreate(
                ['wb_item_id' => $wbItem->id],
                ['stock' => $newCount]
            );
        });
    }

    public function nullNotUpdatedStocks(): void
    {
        Helpers::toBatch(function (Batch $batch) {
            $count = WbWarehouseStock::query()
                ->with('wbItem')
                ->whereHas('wbItem', function (Builder $query) {
                    $query->where('wb_market_id', $this->market->id);
                })
                ->whereHas('warehouse', function (Builder $query) {
                    $query->whereHas('suppliers', function (Builder $query) {
                        $query
                            ->where('supplier_id', $this->supplier->id)
                            ->when($this->supplierWarehousesIds, function (Builder $query) {
                                $query->whereHas('warehouses', function (Builder $query) {
                                    $query->whereIn('supplier_warehouse_id', $this->supplierWarehousesIds);
                                });
                            });
                    });
                })->count();
            $offset = 0;
            while ($count > $offset) {
                $batch->add(new NullNotUpdatedStocksBatch($this, $offset));
                $offset += 10000;
            }
        }, 'market-unload');

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
                        if ($wbItem->itemable->supplier_id === $this->supplier->id) {
                            return true;
                        }
                    } else {
                        if ($wbItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
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
        if (!$this->market->enabled_stocks) {
            SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: пропускаем выгрузку остатков в кабинет");
            return;
        }

        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка остатков в кабинет");

        if (!$this->market->warehouses()->count()) {
            SupplierReportService::addLog($this->supplier, "Нет складов. Остатки не выгружены");
            return;
        }

        $this->market->warehouses()
            ->whereHas('suppliers', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id)
                    ->when($this->supplierWarehousesIds, function (Builder $query) {
                        $query->whereHas('warehouses', function (Builder $query) {
                            $query->whereIn('supplier_warehouse_id', $this->supplierWarehousesIds);
                        });
                    });
            })
            ->get()
            ->map(function (WbWarehouse $warehouse) {

                SupplierReportService::addLog($this->supplier, "Склад {$warehouse->name}: выгрузка остатков");

                $this->market
                    ->items()
                    ->with('itemable')
                    ->chunk(1000, function (Collection $items) use ($warehouse) {

                        /** @var Collection $data */
                        $data = $items->filter(function (WbItem $wbItem) {

                            if ($wbItem->wbitemable_type === Item::class) {
                                if ($wbItem->itemable->supplier_id === $this->supplier->id) {
                                    return true;
                                }
                            } else {
                                if ($wbItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
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

                        if (App::isProduction() && $data->isNotEmpty()) {
                            $wbClient = new WbClient($this->market->api_key);
                            $wbClient->putStocks($data->values(), $warehouse->warehouse_id, $this->supplier, $this->market);
                        }

                    });
            });
    }

    public function unloadAllPrices(): void
    {
        if (!$this->market->enabled_price) {
            SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: пропускаем выгрузку цен в кабинет");
            return;
        }

        SupplierReportService::changeMessage($this->supplier, "Кабинет ВБ {$this->market->name}: выгрузка цен в кабинет");

        $this->market
            ->items()
            ->with('itemable')
            ->whereNotNull('volume')
            ->whereNotNull('retail_markup_percent')
            ->whereNotNull('package')
            ->whereNotNull('sales_percent')
            ->whereNotNull('min_price')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->whereNotNull('nm_id')
            ->where('price', '<>', DB::raw('last_price'))
            ->chunk(1000, function (Collection $items) {

                /** @var Collection $data */
                $data = $items->filter(function (WbItem $wbItem) {

                    if ($wbItem->wbitemable_type === Item::class) {
                        if ($wbItem->itemable->supplier_id === $this->supplier->id) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->itemable->updated) {
                                    return true;
                                }
                            } else {
                                return true;
                            }
                        }
                    } else {
                        if ($wbItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                if ($wbItem->itemable->items->every(fn(Item $item) => $item->updated)) {
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

                if (App::isProduction() && $data->isNotEmpty()) {
                    $wbClient = new WbClient($this->market->api_key);
                    $wbClient->putPrices($data->values(), $this->supplier, $this->market);
                }

            });
    }
}
