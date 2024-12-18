<?php

namespace App\Services;

use App\Contracts\MarketItemStockContract;
use App\HttpClient\OzonClient\OzonClient;
use App\HttpClient\WbClient\WbClient;
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
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use App\Models\WbWarehouseSupplier;
use App\Models\WbWarehouseSupplierWarehouse;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\Moysklad\Services\MoyskladItemOrderService;

class MarketItemStockService implements MarketItemStockContract
{
    public User $user;
    public WbMarket|OzonMarket $market;
    public Supplier $supplier;
    public ReportLog $log;
    public array $supplierWarehousesIds;
    public bool $enabledOrderModule;
    public bool $enabledMoyskladModule;

    public function make(Supplier $supplier, WbMarket|OzonMarket $market, ReportLog $log, array $supplierWarehousesIds): void
    {
        $this->user = $market->user;
        $this->market = $market;
        $this->supplier = $supplier;
        $this->log = $log;
        $this->supplierWarehousesIds = $supplierWarehousesIds;
        $this->market = $this->market->load([
            'warehouses',
            'warehouses.suppliers.warehouses',
            'warehouses.userWarehouses.warehouse.stocks'
        ]);
        $this->enabledMoyskladModule = ModuleService::moduleIsEnabled('Order', $this->user);
        $this->enabledOrderModule = ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders;
    }

    public function updateStock(): void
    {
        $log = SupplierReportLogMarketService::new($this->log, 'Обновление остатков');

        try {
            $this->market
                ->items()
                ->whereHasMorph('itemable', [Item::class], function (Builder $query) {
                    $query->where('supplier_id', $this->supplier->id);
                })
                ->with(['itemable.warehousesStocks', 'itemable.supplierWarehouseStocks', 'itemable.moyskladOrders'])
                ->lazy()
                ->each(function (OzonItem|WbItem $item) {
                    $this->recountStockItem($item);
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
                ->each(function (OzonItem|WbItem $item) {
                    $this->recountStockBundle($item);
                });
        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }

    public function recountStockItem(OzonItem|WbItem $item): void
    {
        $this->market->warehouses->each(function (OzonWarehouse|WbWarehouse $warehouse) use ($item) {

            $unload = $item instanceof WbItem ? $item->itemable->unload_wb : $item->itemable->unload_ozon;

            /** @var OzonWarehouseSupplier|WbWarehouseSupplier $marketWarehouseSupplier */
            $marketWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$marketWarehouseSupplier) return;

            if ($unload) {

                $supplierWarehousesIds = $marketWarehouseSupplier->warehouses
                    ->filter(fn(OzonWarehouseSupplierWarehouse|WbWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $item->itemable
                    ->warehousesStocks
                    ->filter(fn(ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                    ->sum('stock');

                $newCount = $item->itemable
                    ->supplierWarehouseStocks
                    ->filter(fn(ItemSupplierWarehouseStock $stock) => in_array($stock->supplier_warehouse_id, $supplierWarehousesIds))
                    ->sum('stock');
                $multiplicity = $item->itemable->multiplicity;

                $newCount = $newCount - $this->market->minus_stock;
                $newCount = $newCount < $this->market->min ? 0 : $newCount;
                $newCount = ($newCount >= $this->market->min && $newCount <= $this->market->max && $multiplicity === 1) ? 1 : $newCount;
                $newCount = ($newCount + $myWarehousesStocks) / $multiplicity;

                if ($this->market->enabled_orders) {

                    if ($this->enabledOrderModule) {
                        $newCount = $newCount - ($item->orders()->where('state', 'new')->sum('count') * $multiplicity);
                    }

                    if ($this->enabledMoyskladModule) {
                        $newCount = $newCount - (($item->itemable->moyskladOrders->firstWhere('new', true) ? MoyskladItemOrderService::getOrders($item->itemable)->sum('orders') : 0) * $item->itemable->multiplicity);
                    }

                }

                $newCount = $newCount > $this->market->max_count ? $this->market->max_count : $newCount;
                $newCount = (int)max($newCount, 0);

            } else {
                $newCount = 0;
            }

            if ($this->market instanceof WbMarket) {
                $warehouse->stocks()->updateOrCreate(
                    ['wb_item_id' => $item->id],
                    ['stock' => $newCount]
                );
            } else {
                $warehouse->stocks()->updateOrCreate(
                    ['ozon_item_id' => $item->id],
                    ['stock' => $newCount]
                );
            }
        });
    }

    public function recountStockBundle(OzonItem|WbItem $item): void
    {
        $this->market->warehouses->each(function (OzonWarehouse|WbWarehouse $warehouse) use ($item) {

            $unload = $item instanceof WbItem ? boolval($item->itemable->items->firstWhere('unload_wb', false)) : boolval($item->itemable->items->firstWhere('unload_ozon', false));

            /** @var OzonWarehouseSupplier|WbWarehouseSupplier $marketWarehouseSupplier */
            $marketWarehouseSupplier = $warehouse->suppliers
                ->firstWhere('supplier_id', $this->supplier->id);

            if (!$marketWarehouseSupplier) return;

            if ($unload) {

                $supplierWarehousesIds = $marketWarehouseSupplier->warehouses
                    ->filter(fn(OzonWarehouseSupplierWarehouse|WbWarehouseSupplierWarehouse $warehouse) => $warehouse->supplier_warehouse_id, $this->supplierWarehousesIds)
                    ->pluck('supplier_warehouse_id')
                    ->toArray();

                $myWarehousesStocks = $item->itemable
                    ->items
                    ->sum(function (Item $item) use ($warehouse) {
                        return $item
                            ->warehousesStocks
                            ->filter(fn(ItemWarehouseStock $stock) => in_array($stock->warehouse_id, $warehouse->userWarehouses->pluck('warehouse_id')->toArray()))
                            ->sum(fn(ItemWarehouseStock $stock) => $stock->stock / $item->pivot->multiplicity);
                    });

                $newCount = $item->itemable
                    ->items
                    ->map(function (Item $item) use ($supplierWarehousesIds) {
                        $count = $item
                            ->supplierWarehouseStocks
                            ->filter(fn(ItemSupplierWarehouseStock $stock) => in_array($stock->supplier_warehouse_id, $supplierWarehousesIds))
                            ->sum(fn(ItemSupplierWarehouseStock $stock) => $stock->stock / $item->pivot->multiplicity);

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

            if ($this->market instanceof WbMarket) {
                $warehouse->stocks()->updateOrCreate(
                    ['wb_item_id' => $item->id],
                    ['stock' => $newCount]
                );
            } else {
                $warehouse->stocks()->updateOrCreate(
                    ['ozon_item_id' => $item->id],
                    ['stock' => $newCount]
                );
            }
        });
    }

    public function nullAllStocks(): void
    {
        $relation = $this->market instanceof WbMarket ? 'wbItem' : 'ozonItem';
        $stockClass = $this->market instanceof WbMarket ? WbWarehouseStock::class : OzonWarehouseStock::class;

        $stockClass::query()->whereHas($relation, fn(Builder $query) => $this->filteredData($query))->update(['stock' => 0]);
    }

    public function filteredData(Builder $query): Builder
    {
        return $query->whereHasMorph('itemable', [Item::class, Bundle::class], function (Builder $query, $type) {
            if ($type === Item::class) {
                $query->where('supplier_id', $this->supplier->id);
            } else {
                $query
                    ->whereHasMorph('itemable', [Bundle::class], function (Builder $query) {
                        $query->whereHas('items', function (Builder $query) {
                            $query->where('supplier_id', $this->supplier->id);
                        });
                    });
            }
        })
            ->where('wb_market_id', $this->market->id);
    }

    public function unloadAllStocks(): void
    {
        if (!$this->market->enabled_stocks || !$this->market->warehouses()->count()) {
            $log = SupplierReportLogMarketService::new($this->log, 'Пропускаем выгрузку остатков в кабинет');
            SupplierReportLogMarketService::failed($log);
            return;
        }

        $log = SupplierReportLogMarketService::new($this->log, 'Выгрузка остатков');

        try {

            $warehouses = $this->market->warehouses()
                ->whereHas('suppliers', function (Builder $query) {
                    $query
                        ->where('supplier_id', $this->supplier->id)
                        ->when($this->supplierWarehousesIds, function (Builder $query) {
                            $query->whereHas('warehouses', function (Builder $query) {
                                $query->whereIn('supplier_warehouse_id', $this->supplierWarehousesIds);
                            });
                        });
                })
                ->get();

            $itemsQuery = $this->market
                ->items()
                ->with('itemable')
                ->where(fn(Builder $query) => $this->filteredData($query));

            if ($this->market instanceof WbMarket) {

                $warehouses->map(function (WbWarehouse $warehouse) use ($itemsQuery) {
                    $itemsQuery->chunk(1000, function (Collection $items) use ($warehouse) {

                        /** @var Collection $data */
                        $data = $items->map(function (WbItem $item) use ($warehouse) {
                            return [
                                "sku" => (string)$item->sku,
                                "amount" => (int)($item->warehouseStock($warehouse) ? $item->warehouseStock($warehouse)->stock : 0),
                            ];
                        });

                        if (App::isProduction() && $data->isNotEmpty()) {
                            $wbClient = new WbClient($this->market->api_key);
                            $wbClient->putStocks($data->values(), $warehouse->warehouse_id, $this->market, $this->log);
                        }

                    });
                });

            } else {

                $warehouses->map(function (OzonWarehouse $warehouse) use ($itemsQuery) {
                    $itemsQuery->chunk(100, function (Collection $items) use ($warehouse) {

                        /** @var Collection $data */
                        $data = $items->map(function (OzonItem $item) use ($warehouse) {
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

            }

        } catch (\Throwable $th) {
            report($th);
            SupplierReportLogMarketService::failed($log);
            return;
        }

        SupplierReportLogMarketService::finished($log);
    }
}
