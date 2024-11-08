<?php

namespace Modules\Order\Services;

use Alcohol\ISO4217;
use App\Models\Bundle;
use App\Models\Organization;
use App\Models\OzonItem;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WbItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Order\Exports\SupplierOrderExport;
use Modules\Order\HttpClient\WbClient;
use Modules\Order\HttpClient\OzonClient;
use Modules\Order\Models\Order;
use Modules\Order\Models\SupplierOrderReport;
use Modules\Order\Models\WriteOffItemWarehouseStock;

class OrderService
{
    public function __construct(public string $organizationId, public User $user)
    {
    }


    public function getOrders(): int
    {
        $total = 0;

        $ozonMarkets = $this->user->ozonMarkets()->where('organization_id', $this->organizationId)->get();
        $wbMarkets = $this->user->wbMarkets()->where('organization_id', $this->organizationId)->get();

        // WB

        foreach ($wbMarkets as $wbMarket) {
            $client = new WbClient($wbMarket->api_key);

            $orders = $client->getNewOrders();

            $orders = $orders
                ->groupBy('id')
                ->map(function (Collection $group) {
                    return $group->reduce(function ($carry, $item) {
                        if (is_null($carry)) {
                            $carry = $item;
                            $carry['count'] = $item['count'] ?? 1;
                        } else {
                            $carry['count'] += $item['count'] ?? 1;
                        }
                        return $carry;
                    });
                })
                ->values();

            $orders->each(function (array $order) use ($wbMarket, &$total) {

                $order = collect($order);

                $wbItem = $wbMarket->items()->where('vendor_code', $order->get('article'))->first();

                if ($wbItem && !Order::where('organization_id', $this->organizationId)->where('number', $order->get('id'))->exists()) {

                    $iso4217 = new ISO4217();

                    try {
                        $currency_code = collect($iso4217->getByNumeric($order->get('currencyCode')))->get('alpha3');
                    } catch (\DomainException $e) {
                        $currency_code = 'Не определено';
                    }

                    /** @var Order $orderModel */
                    $orderModel = $wbItem->orders()->create([
                        'number' => $order->get('id'),
                        'count' => $order->get('count'),
                        'price' => $order->get('price') / 100,
                        'organization_id' => $this->organizationId,
                        'currency_code' => $currency_code,
                    ]);

                    if ($wbItem->itemable instanceof Bundle) {
                        foreach ($wbItem->itemable->items as $item) {
                            $orderModel->items()->create([
                                'item_id' => $item->id,
                            ]);
                        }
                    } else {
                        $orderModel->items()->create([
                            'item_id' => $wbItem->itemable->id,
                        ]);
                    }

                    $total++;

                }
            });
        }

        // OZON

        foreach ($ozonMarkets as $ozonMarket) {

            $allPostings = collect();
            $limit = 1000;
            $offset = 0;

            do {
                $client = new OzonClient($ozonMarket->api_key, $ozonMarket->client_id);
                $result = $client->getNewOrders($limit, $offset);
                $postings = collect($result->get('postings'));
                $hasNext = $result->get('has_next');

                $offset += $limit;

                $allPostings = $allPostings->merge($postings);
            } while ($hasNext);

            $allPostings->each(function (array $posting) use ($ozonMarket, &$total) {

                $posting = collect($posting);

                collect($posting->get('products'))->each(function (array $product) use ($posting, $ozonMarket, &$total) {

                    $product = collect($product);

                    $ozonItem = $ozonMarket->items()->where('offer_id', $product->get('offer_id'))->first();

                    if ($ozonItem && !Order::where('organization_id', $this->organizationId)->where('number', $posting->get('posting_number'))->exists()) {

                        $orderModel = $ozonItem->orders()->create([
                            'number' => $posting->get('posting_number'),
                            'count' => $product->get('quantity'),
                            'price' => $product->get('price'),
                            'organization_id' => $this->organizationId,
                            'currency_code' => $product->get('currency_code')
                        ]);

                        if ($ozonItem->itemable instanceof Bundle) {
                            foreach ($ozonItem->itemable->items as $item) {
                                $orderModel->items()->create([
                                    'item_id' => $item->id,
                                ]);
                            }
                        } else {
                            $orderModel->items()->create([
                                'item_id' => $ozonItem->itemable->id,
                            ]);
                        }

                        $total++;
                    }
                });

            });
        }

        return $total;
    }

    public function writeOffBalance(array $warehouses): int
    {
        $total = 0;

        $warehouses = collect(Arr::map($warehouses, function ($warehouseId) {
            return Warehouse::findOrFail($warehouseId);
        }));

        DB::transaction(function () use ($warehouses, &$total) {
            Order::where('organization_id', $this->organizationId)->whereHas('orderable')->where('state', 'new')->chunk(100, function (Collection $orders) use ($warehouses, &$total) {
                $orders->each(function (Order $order) use ($warehouses, &$total) {
                    $warehouses->each(function (Warehouse $warehouse) use ($order, &$total) {
                        $stock = $warehouse->stocks()->where('item_id', $order->orderable->item_id)->first();
                        if ($stock && $stock->stock > 0 && $order->count - $order->writeOffStocks->first()?->stock > 0) {
                            if ($stock->stock - $order->count >= 0) {

                                $total++;

                                $stock->stock -= $order->count;
                                if ($writeOff = $order->writeOffStocks()->where('item_warehouse_stock_id', $stock->id)->first()) {
                                    $writeOff->stock += $order->count;
                                    $writeOff->save();
                                } else {
                                    $order->writeOffStocks()->create([
                                        'stock' => $order->count,
                                        'item_warehouse_stock_id' => $stock->id,
                                    ]);
                                }
                                $order->count = 0;
                            } else {

                                $total++;

                                if ($writeOff = $order->writeOffStocks()->where('item_warehouse_stock_id', $stock->id)->first()) {
                                    $writeOff->stock += $stock->stock;
                                    $writeOff->save();
                                } else {
                                    $order->writeOffStocks()->create([
                                        'stock' => $stock->stock,
                                        'item_warehouse_stock_id' => $stock->id,
                                    ]);
                                }
                                $order->count -= $stock->stock;
                                $stock->stock = 0;
                            }
                            $stock->save();
                            $order->save();
                        }
                    });
                });
            });
        });

        return $total;
    }

    public function writeOffBalanceRollback(): void
    {
        WriteOffItemWarehouseStock::whereHas('order', function (Builder $query) {
            $query->where('organization_id', $this->organizationId);
        })
            ->chunk(100, function (Collection $writeOffStocks) {
                $writeOffStocks->each(function (WriteOffItemWarehouseStock $writeOffStock) {

                    $itemWarehouseStock = $writeOffStock->itemWarehouseStock;
                    $itemWarehouseStock->stock = $itemWarehouseStock->stock + $writeOffStock->stock;
                    $order = $writeOffStock->order;
                    $order->count += $writeOffStock->stock;

                    $itemWarehouseStock->save();
                    $order->save();
                    $writeOffStock->delete();

                });
            });
    }

    public function clearAll(): void
    {
        $organization = Organization::find($this->organizationId);
        $organization->orders()->chunk(100, function (Collection $orders) {
            $orders->each(function (Order $order) {

                $order->writeOffStocks()->delete();
                $order->markAccepted();
            });
        });
        $organization->supplierOrderReports()->delete();
    }

    public static function prune(): void
    {
        Order::where('updated_at', '<', now()->subMonth())->delete();
    }

    public function purchaseOrder()
    {
        $organization = Organization::find($this->organizationId);

        $organization->supplierOrderReports()->delete();

        $organization
            ->orders()
            ->with('orderable.itemable')
            ->where('state', 'new')
            ->get()
            ->groupBy('items.supplier_id')
            ->each(function (Collection $hh, string $supplierId) {

                $uuid = Str::uuid();

                \Excel::store(new SupplierOrderExport($this->organizationId, $supplierId), "users/orders/{$uuid}.xlsx", 'public');

                SupplierOrderReport::create([
                    'supplier_id' => $supplierId,
                    'organization_id' => $this->organizationId,
                    'uuid' => $uuid,
                ]);
            });
    }

    public function processOrders(): void
    {
        $organization = Organization::find($this->organizationId);

        $this->getOrders();
        $this->writeOffBalance($organization->selectedOrdersWarehouses->map(fn (Warehouse $warehouse) => $warehouse->id)->toArray());
        $this->purchaseOrder();

        $ozonService = new OzonOrderService($organization, $this->user);
        $ozonService->writeOffStocks();

        $wbService = new WbOrderService($organization, $this->user);
        $wbService->writeOffStocks();

        $ozonService->setStates();
    }
}
