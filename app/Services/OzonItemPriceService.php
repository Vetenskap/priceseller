<?php

namespace App\Services;

use App\HttpClient\OzonClient;
use App\Models\Bundle;
use App\Models\Item;
use App\Models\ItemWarehouseStock;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use App\Models\OzonWarehouseUserWarehouse;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Modules\Moysklad\Models\Moysklad;
use Modules\Order\Models\Order;

class OzonItemPriceService
{
    protected OzonClient $ozonClient;
    protected User $user;

    public function __construct(public ?Supplier $supplier = null, public OzonMarket $market)
    {
        $this->ozonClient = new OzonClient($this->market->api_key, $this->market->client_id);
        $this->user = $this->market->user;
    }

    public function updatePrice(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт цен");

        OzonItem::query()
            ->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                if ($type === Item::class) {
                    $query
                        ->where('supplier_id', $this->supplier->id)
                        ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                            $query->where('updated', true);
                        });
                } elseif ($type === Bundle::class) {
                    $query
                        ->whereHas('items', function (Builder $query) {
                            $query->where('supplier_id', $this->supplier->id);
                        })
                        ->whereDoesntHave('items', function (Builder $query) {
                            $query
                                ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                                    $query->where('updated', false);
                                });
                        });
                }
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
            ->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                if ($type === Item::class) {
                    $query->where('supplier_id', $this->supplier->id);
                } elseif ($type === Bundle::class) {
                    $query->whereHas('items', function (Builder $query) {
                        $query->where('supplier_id', $this->supplier->id);
                    });
                }
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
        if ($ozonItem->ozonitemable_type === 'App\Models\Item') {

            $multiplicity = $ozonItem->ozonitemable->multiplicity;

            if ($this->user->baseSettings?->enabled_use_buy_price_reserve && !$ozonItem->ozonitemable->price) {
                $price = $ozonItem->ozonitemable->buy_price_reserve;
            } else {
                $price = $ozonItem->ozonitemable->price;
            }

        } else {

            $multiplicity = 1;

            $price = $ozonItem->ozonitemable->items->map(function (Item $item) {
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

        return $ozonItem;
    }

    public function updateStock(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: перерасчёт остатков");

        OzonItem::query()
            ->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                if ($type === Item::class) {
                    $query
                        ->where('supplier_id', $this->supplier->id)
                        ->where('unload_ozon', true)
                        ->where('updated', true);
                } elseif ($type === Bundle::class) {
                    $query
                        ->whereHas('items', function (Builder $query) {
                            $query->where('supplier_id', $this->supplier->id);
                        })
                        ->whereDoesntHave('items', function (Builder $query) {
                            $query
                                ->where('unload_ozon', false)
                                ->where('updated', false);
                        });
                }
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
        $this->market->warehouses->each(function (OzonWarehouse $warehouse) use ($ozonItem) {

            $new_count = 0;

            if ($ozonItem->ozonitemable_type === 'App\Models\Item') {
                $unload_ozon = !$ozonItem->ozonitemable->unload_ozon;
            } else {
                $unload_ozon = boolval($ozonItem->ozonitemable->items->first(fn(Item $item) => !$item->unload_ozon));
            }

            if (!$unload_ozon) {

                $itemIds = $ozonItem->ozonitemable_type === 'App\Models\Item' ? [$ozonItem->ozonitemable_id] : $ozonItem->ozonitemable->items->pluck('id')->toArray();

                $myWarehousesStocks = $warehouse->userWarehouses->map(function (OzonWarehouseUserWarehouse $userWarehouse) use ($ozonItem, $itemIds) {
                    return $userWarehouse->warehouse->stocks()->whereIn('item_id', $itemIds)->map(fn(ItemWarehouseStock $stock) => $stock->stock)->sum();
                })->sum();

                if ($ozonItem->ozonitemable_type === 'App\Models\Item') {
                    $new_count = $ozonItem->ozonitemable->count;
                    $multiplicity = $ozonItem->ozonitemable->multiplicity;
                } else {
                    $new_count = $ozonItem->ozonitemable->items->map(function (Item $item) {

                        $count = $item->count / $item->pivot->multiplicity;

                        if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                            $count = $count - (($item->moyskladOrders()->where('new', true)->exists() ? $item->moyskladOrders()->where('new')->sum('orders') : 0));
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
                    $new_count = $new_count - ($ozonItem->orders()->where('state', 'new')->sum('count') * $multiplicity);
                }

                if ($ozonItem->ozonitemable_type === 'App\Models\Item') {
                    if (ModuleService::moduleIsEnabled('Moysklad', $this->user) && $this->user->moysklad && $this->user->moysklad->enabled_orders) {
                        $new_count = $new_count - (($ozonItem->ozonitemable->moyskladOrders()->where('new', true)->exists() ? $ozonItem->ozonitemable->moyskladOrders()->where('new')->sum('orders') : 0) * $ozonItem->ozonitemable->multiplicity);
                    }
                }

                $new_count = $new_count > $this->market->max_count ? $this->market->max_count : $new_count;
                $new_count = (int)max($new_count, 0);

            }

            $warehouse->stocks()->updateOrCreate([
                'ozon_item_id' => $ozonItem->id
            ], [
                'ozon_item_id' => $ozonItem->id,
                'stock' => $new_count
            ]);

        });

        return $ozonItem;
    }

    public function nullNotUpdatedStocks(): void
    {
        OzonWarehouseStock::query()
            ->whereHas('ozonItem', function (Builder $query) {
                $query->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                    if ($type === Item::class) {
                        $query
                            ->where('supplier_id', $this->supplier->id)
                            ->where('updated', false)
                            ->orWhere('unload_ozon', false);
                    } elseif ($type === Bundle::class) {
                        $query
                            ->whereHas('items', function (Builder $query) {
                                $query
                                    ->where('supplier_id', $this->supplier->id)
                                    ->where('updated', false)
                                    ->orWhere('unload_ozon', false);
                            });
                    }
                });
            })
            ->update(['stock' => 0]);
    }

    public function nullAllStocks(): void
    {
        OzonWarehouseStock::query()
            ->whereHas('ozonItem', function (Builder $query) {
                $query->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                    if ($type === Item::class) {
                        $query->where('supplier_id', $this->supplier->id);
                    } elseif ($type === Bundle::class) {
                        $query
                            ->whereHas('items', function (Builder $query) {
                                $query->where('supplier_id', $this->supplier->id);
                            });
                    }
                });
            })
            ->update(['stock' => 0]);
    }

//    public function unloadOzonItemStocks(OzonItem $ozonItem): void
//    {
//        $this->market->warehouses()
//            ->whereHas('market', function (Builder $query) use ($ozonItem) {
//                $query->whereHas('items', function (Builder $query) use ($ozonItem) {
//                    $query->where('id', $ozonItem->item_id);
//                });
//            })
//            ->get()
//            ->map(function (OzonWarehouse $warehouse) use ($ozonItem) {
//
//                $data = [
//                    [
//                        'offer_id' => (string)$ozonItem->offer_id,
//                        'product_id' => (int)$ozonItem->product_id,
//                        'stock' => (int)$ozonItem->warehouseStock($warehouse) ? $ozonItem->warehouseStock($warehouse)->stock : 0,
//                        'warehouse_id' => (int)$warehouse->warehouse_id
//                    ]
//                ];
//
//                if (App::isProduction()) {
//                    $this->ozonClient->putStocks($data, $this->supplier);
//                }
//            });
//    }

    public function unloadAllStocks(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: выгрузка остатков в кабинет");

        if (!$this->market->warehouses()->count()) {
            SupplierReportService::addLog($this->supplier, "Нет складов. Остатки не выгружены");
            return;
        }

        $this->market->warehouses()
            ->whereHas('suppliers', function (Builder $query) {
                $query
                    ->where('supplier_id', $this->supplier->id);
            })
            ->get()
            ->map(function (OzonWarehouse $warehouse) {

                SupplierReportService::addLog($this->supplier, "Склад {$warehouse->name}: выгрузка остатков");

                OzonItem::query()
                    ->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                        if ($type === Item::class) {
                            $query->where('supplier_id', $this->supplier->id);
                        } elseif ($type === Bundle::class) {
                            $query->whereHas('items', function (Builder $query) {
                                $query->where('supplier_id', $this->supplier->id);
                            });
                        }
                    })
                    ->chunk(100, function (Collection $items) use ($warehouse) {

                        $data = $items->map(function (OzonItem $item) use ($warehouse) {
                            return [
                                'offer_id' => (string)$item->offer_id,
                                'product_id' => (int)$item->product_id,
                                'stock' => (int)($item->warehouseStock($warehouse) ? $item->warehouseStock($warehouse)->stock : 0),
                                'warehouse_id' => (int)$warehouse->warehouse_id
                            ];
                        });

                        if (App::isProduction()) {
                            $this->ozonClient->putStocks($data->all(), $this->supplier);
                        }

                    });
            });
    }

    public function unloadAllPrices(): void
    {
        SupplierReportService::changeMessage($this->supplier, "Кабинет ОЗОН {$this->market->name}: цен в кабинет");

        OzonItem::query()
            ->whereHasMorph('ozonitemable', [Item::class, Bundle::class], function (Builder $query, $type) {
                if ($type === Item::class) {
                    $query
                        ->where('supplier_id', $this->supplier->id)
                        ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                            $query->where('updated', true);
                        });
                } elseif ($type === Bundle::class) {
                    $query
                        ->whereHas('items', function (Builder $query) {
                            $query->where('supplier_id', $this->supplier->id);
                        })
                        ->whereDoesntHave('items', function (Builder $query) {
                            $query
                                ->when(!$this->user->baseSettings()->exists() || !$this->user->baseSettings->enabled_use_buy_price_reserve, function (Builder $query) {
                                    $query->where('updated', false);
                                });
                        });
                }
            })
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
                }
            });
    }
}
