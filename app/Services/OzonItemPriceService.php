<?php

namespace App\Services;

use App\Contracts\ReportLogContract;
use App\HttpClient\OzonClient\OzonClient;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\ItemSupplierWarehouseStock;
use App\Models\ItemWarehouseStock;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use App\Models\OzonWarehouseSupplier;
use App\Models\OzonWarehouseSupplierWarehouse;
use App\Models\ReportLog;
use App\Models\Supplier;
use App\Models\User;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Modules\Moysklad\Services\MoyskladItemOrderService;

class OzonItemPriceService
{
    protected User $user;
    public bool $enabledOrderModule;
    public bool $enabledMoyskladModule;
    public ReportLogContract $reportLogContract;

    public function __construct(public Supplier $supplier, public OzonMarket $market, public array $supplierWarehousesIds, public ReportLog $log)
    {
        $this->reportLogContract = \app(ReportLogContract::class);
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
        $log = SupplierReportLogMarketService::new($this->log, 'Обновление цен');
        SupplierReportLogMarketService::running($log);

        try {
            $this->market
                ->items()
                ->whereHasMorph('itemable', [Item::class], function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                })
                ->with(['itemable'])
                ->lazy()
                ->each(function (OzonItem $ozonItem) {
                    $this->recountPriceOzonItem($ozonItem);
                });
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }

    public function recountPriceOzonItem(OzonItem $ozonItem): void
    {
        if ($ozonItem->ozonitemable_type === 'App\Models\Item') {

            $multiplicity = $ozonItem->itemable->multiplicity;

            if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$ozonItem->itemable->price) {
                $price = $ozonItem->itemable->buy_price_reserve;
            } else {
                $price = $ozonItem->itemable->price;
            }

        } else {

            $multiplicity = 1;

            $price = $ozonItem->itemable->items->map(function (Item $item) {
                if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$item->price) {
                    return $item->buy_price_reserve * $item->pivot->multiplicity;
                } else {
                    return $item->price * $item->pivot->multiplicity;
                }
            })->sum();
        }

        $min_price_percent = (float)$this->market->min_price_percent;
        $seller_price_percent = (float)$this->market->seller_price_percent;
        $max_price_percent = (float)$this->market->max_price_percent / 100 + 1;
        $acquiring = (float)$this->market->acquiring;
        $lastMile = (float)$this->market->last_mile;
        $maxMile = (float)$this->market->max_mile;

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

        $ozonItem->save();
    }

    public function updateStock(): void
    {
        $log = SupplierReportLogMarketService::new($this->log, 'Обновление остатков');
        SupplierReportLogMarketService::running($log);

        try {
            $this->market
                ->items()
                ->whereHasMorph('itemable', [Item::class], function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                })
                ->with(['itemable.warehousesStocks', 'itemable.supplierWarehouseStocks', 'itemable.moyskladOrders'])
                ->lazy()
                ->each(function (OzonItem $item) {
                    $this->recountStockOzonItem($item);
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
                ->each(function (OzonItem $item) {
                    $this->recountStockOzonItem($item);
                });

            $this->nullNotUpdatedStocks();
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }

    public function recountStockOzonItem(OzonItem $ozonItem): void
    {
        if ($ozonItem->ozonitemable_type === Item::class) {
            $this->recountStockItem($ozonItem);
        } else {
            $this->recountStockBundle($ozonItem);
        }
    }

    private function recountStockItem(OzonItem $ozonItem): void
    {
        $this->market->warehouses->each(function (OzonWarehouse $warehouse) use ($ozonItem) {

            /** @var OzonWarehouseSupplier $ozonWarehouseSupplier */
            $ozonWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$ozonWarehouseSupplier) return;

            if ($ozonItem->itemable->unload_ozon) {

                $supplierWarehousesIds = $ozonWarehouseSupplier->warehouses
                    ->filter(fn(OzonWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $ozonItem->itemable
                    ->warehousesStocks
                    ->filter(fn (ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                    ->sum('stock');

                $newCount = $ozonItem->itemable
                    ->supplierWarehouseStocks
                    ->filter(fn (ItemSupplierWarehouseStock $stock) => in_array($stock->supplier_warehouse_id, $supplierWarehousesIds))
                    ->sum('stock');
                $multiplicity = $ozonItem->itemable->multiplicity;

                $newCount = $newCount - $this->market->minus_stock;
                $newCount = $newCount < $this->market->min ? 0 : $newCount;
                $newCount = ($newCount >= $this->market->min && $newCount <= $this->market->max && $multiplicity === 1) ? 1 : $newCount;
                $newCount = ($newCount + $myWarehousesStocks) / $multiplicity;

                if ($this->market->enabled_orders) {

                    if ($this->enabledOrderModule) {
                        $newCount = $newCount - ($ozonItem->orders()->where('state', 'new')->sum('count') * $multiplicity);
                    }

                    if ($ozonItem->ozonitemable_type === 'App\Models\Item') {
                        if ($this->enabledMoyskladModule) {
                            $newCount = $newCount - (($ozonItem->itemable->moyskladOrders->firstWhere('new', true) ? MoyskladItemOrderService::getOrders($ozonItem->itemable)->sum('orders') : 0) * $ozonItem->itemable->multiplicity);
                        }
                    }

                }

                $newCount = $newCount > $this->market->max_count ? $this->market->max_count : $newCount;
                $newCount = (int)max($newCount, 0);

            } else {
                $newCount = 0;
            }

            $warehouse->stocks()->updateOrCreate(
                ['ozon_item_id' => $ozonItem->id],
                ['stock' => $newCount]
            );
        });
    }

    private function recountStockBundle(OzonItem $ozonItem): void
    {
        $this->market->warehouses->each(function (OzonWarehouse $warehouse) use ($ozonItem) {

            /** @var OzonWarehouseSupplier $ozonWarehouseSupplier */
            $ozonWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$ozonWarehouseSupplier) return;

            if (!boolval($ozonItem->itemable->items->firstWhere('unload_wb', false))) {

                $supplierWarehousesIds = $ozonWarehouseSupplier->warehouses
                    ->filter(fn(OzonWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $ozonItem->itemable
                    ->items
                    ->sum(function (Item $item) use ($warehouse) {
                        return $item
                            ->warehousesStocks
                            ->filter(fn (ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                            ->sum(fn (ItemWarehouseStock $stock) => $stock->stock / $item->pivot->multiplicity);
                    });

                $newCount = $ozonItem->itemable
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
                ['ozon_item_id' => $ozonItem->id],
                ['stock' => $newCount]
            );
        });
    }

    public function nullNotUpdatedStocks(): void
    {
        OzonWarehouseStock::query()
            ->whereHas('ozonItem', function (Builder $query) {
                $query
                    ->whereHasMorph('itemable', [Item::class], function (Builder $query) {
                        $query->where('unload_wb', false);
                    })
                    ->where('ozon_market_id', $this->market->id);
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
            })->update(['stock' => 0]);

        OzonWarehouseStock::query()
            ->whereHas('ozonItem', function (Builder $query) {
                $query
                    ->whereHasMorph('itemable', [Bundle::class], function (Builder $query) {
                        $query->whereHas('items', function (Builder $query) {
                            $query->where('unload_wb', false);
                        });
                    })
                    ->where('ozon_market_id', $this->market->id);
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
            })->update(['stock' => 0]);

    }

    public function nullAllStocks(): void
    {
        OzonWarehouseStock::query()
            ->with('ozonItem')
            ->whereHas('ozonItem', function (Builder $query) {
                $query->where('ozon_market_id', $this->market->id);
            })
            ->chunk(1000, function (Collection $stocks) {
                $stocks->filter(function (OzonWarehouseStock $stock) {

                    $ozonItem = $stock->ozonItem;

                    if ($ozonItem->ozonitemable_type === Item::class) {
                        if ($ozonItem->itemable->supplier_id === $this->supplier->id) {
                            return true;
                        }
                    } else {
                        if ($ozonItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                            return true;
                        }
                    }

                    return false;

                })->each(fn(OzonWarehouseStock $stock) => $stock->update(['stock' => 0]));
            });
    }

    public function unloadAllStocks(): void
    {
        if (!$this->market->enabled_stocks || !$this->market->warehouses()->count()) {
            $log = SupplierReportLogMarketService::new($this->log, 'Пропускаем выгрузку остатков в кабинет');
            SupplierReportLogMarketService::failed($log);
            return;
        }

        $log = SupplierReportLogMarketService::new($this->log, 'Выгрузка остатков');
        SupplierReportLogMarketService::running($log);

        try {
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
                ->map(function (OzonWarehouse $warehouse) {
                    $this->market
                        ->items()
                        ->with('itemable')
                        ->chunk(100, function (Collection $items) use ($warehouse) {

                            /** @var Collection $data */
                            $data = $items->filter(function (OzonItem $ozonItem) {

                                if ($ozonItem->ozonitemable_type === Item::class) {
                                    if ($ozonItem->itemable->supplier_id === $this->supplier->id) {
                                        return true;
                                    }
                                } else {
                                    if ($ozonItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                                        return true;
                                    }
                                }

                                return false;

                            })->map(function (OzonItem $item) use ($warehouse) {
                                return [
                                    'offer_id' => (string)$item->offer_id,
                                    'product_id' => (int)$item->product_id,
                                    'stock' => (int)($item->warehouseStock($warehouse) ? $item->warehouseStock($warehouse)->stock : 0),
                                    'warehouse_id' => (int)$warehouse->warehouse_id
                                ];
                            });

                            if (App::isProduction() && $data->isNotEmpty()) {
                                $ozonClient = new OzonClient($this->market->api_key, $this->market->client_id);

                                try {
                                    $ozonClient->putStocks($data->values()->all(), $this->supplier, $this->market);
                                } catch (RequestException $e) {
                                    report($e);
                                    $log = SupplierReportLogMarketService::new($this->log, 'Ошибка при выгрузке 100 остатков: ' . $e->getMessage());
                                    SupplierReportLogMarketService::failed($log);
                                }
                            }

                        });
                });
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }
    }

    public function unloadAllPrices(): void
    {
        if (!$this->market->enabled_price) {
            $log = SupplierReportLogMarketService::new($this->log, 'Пропускаем выгрузку цен');
            SupplierReportLogMarketService::failed($log);
            return;
        }

        $log = SupplierReportLogMarketService::new($this->log, 'Выгрузка цен');
        SupplierReportLogMarketService::running($log);

        try {
            $this->market
                ->items()
                ->with('itemable')
                ->whereNotNull('price_min')
                ->whereNotNull('offer_id')
                ->whereNotNull('price_max')
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->whereNotNull('product_id')
                ->whereNotNull('shipping_processing')
                ->whereNotNull('direct_flow_trans')
                ->whereNotNull('sales_percent')
                ->whereNotNull('min_price')
                ->whereNotNull('min_price_percent')
                ->where('price', '<>', DB::raw('last_price'))
                ->chunk(1000, function (Collection $items) {

                    /** @var Collection $data */
                    $data = $items->filter(function (OzonItem $ozonItem) {

                        if ($ozonItem->ozonitemable_type === Item::class) {
                            if ($ozonItem->itemable->supplier_id === $this->supplier->id) {
                                if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                    if ($ozonItem->itemable->updated) {
                                        return true;
                                    }
                                } else {
                                    return true;
                                }
                            }
                        } else {
                            if ($ozonItem->itemable->items->every(fn(Item $item) => $item->supplier_id === $this->supplier->id)) {
                                if (!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve) {
                                    if ($ozonItem->itemable->items->every(fn(Item $item) => $item->updated)) {
                                        return true;
                                    }
                                } else {
                                    return true;
                                }
                            }
                        }

                        return false;

                    })->map(function (OzonItem $item) {

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

                    if (App::isProduction() && $data->isNotEmpty()) {
                        $ozonClient = new OzonClient($this->market->api_key, $this->market->client_id);

                        try {
                            $ozonClient->putPrices($data->values()->all(), $this->supplier);
                        } catch (\Throwable $th) {
                            report($th);
                            $log = SupplierReportLogMarketService::new($this->log, 'Ошибка при выгрузке 100 цен: ' . $th->getMessage());
                            SupplierReportLogMarketService::failed($log);
                        }
                    }
                });
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }
    }
}
