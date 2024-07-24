<?php

namespace Modules\Order\Services;

use App\Models\Organization;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use App\Models\OzonWarehouse;
use App\Models\OzonWarehouseStock;
use App\Models\User;
use App\Services\OzonItemPriceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\Order\HttpClient\OzonClient;
use Modules\Order\Models\NotChangeOzonState;
use Modules\Order\Models\Order;

class OzonOrderService
{
    public function __construct(public Organization $organization, public User $user)
    {
    }

    public function writeOffStocks()
    {
        $this->organization->orders()->where('state', 'new')->where('write_off', false)->whereHas('orderable')->with('orderable')->chunk(100, function (Collection $orders) {
            $orders->each(function (Order $order) {

                $markets = $this
                    ->user
                    ->ozonMarkets()
                    ->when(App::isProduction(), function (Builder $query) use ($order) {
                        $query->whereNot('id', $order->orderable->ozon_market_id);
                    })
                    ->whereHas('items', function (Builder $query) use ($order) {
                        $query->where('item_id', $order->orderable->item_id);
                    })
                    ->get();

                $markets->each(function (OzonMarket $market) use ($order) {

                    $ozonItem = $market->items()->where('item_id', $order->orderable->item_id)->first();

                    $market->warehouses->each(function (OzonWarehouse $warehouse) use ($order, $ozonItem) {
                        $warehouse->stocks()->where('ozon_item_id', $ozonItem->id)->each(function (OzonWarehouseStock $stock) use ($order) {
                            $total = $stock->stock - $order->count + ($order->writeOffStocks()->first() ? $order->writeOffStocks()->first()->stock : 0);
                            $stock->stock = max(0, $total);
                            $stock->save();
                        });
                    });

                    $service = new OzonItemPriceService(null, $market);
                    $service->unloadOzonItemStocks($ozonItem);
                });

                $order->markWriteOff();
            });
        });
    }

    public function setStates(): int
    {
        $total = 0;

        $this->organization
            ->orders()
            ->where('state', 'new')
            ->with('orderable')
            ->where('orderable_type', OzonItem::class)
            ->chunk(100, function (Collection $orders) use (&$total) {
                $orders->each(function (Order $order) use (&$total) {

                    if (!NotChangeOzonState::where('user_id', $this->user->id)->where('item_id', $order->orderable->item_id)->exists()) {
                        $product = [
                            'product_id' => $order->orderable->product_id,
                            'quantity' => $order->count + $order->writeOffStocks->first()?->stock
                        ];

                        /** @var OzonMarket $market */
                        $market = $order->orderable->market;

                        if (App::isProduction()) {
                            $client = new OzonClient($market->api_key, $market->client_id);
                            $result = $client->setState($product, $order->number);
                        } else {
                            $result = collect($order->number);
                        }
                    }

                });
            });

        return $total;
    }
}
