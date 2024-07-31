<?php

namespace Modules\Order\Services;

use App\Models\Organization;
use App\Models\User;
use App\Models\WbItem;
use App\Models\WbMarket;
use App\Models\WbWarehouse;
use App\Models\WbWarehouseStock;
use App\Services\WbItemPriceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Modules\Order\Models\Order;

class WbOrderService
{
    public function __construct(public Organization $organization, public User $user)
    {
    }

    public function writeOffStocks()
    {
        $this->organization->orders()->where('state', 'new')->where('write_off', false)->with('orderable')->chunk(100, function (Collection $orders) {
            $orders->each(function (Order $order) {

                $markets = $this
                    ->user
                    ->wbMarkets()
                    ->when(App::isProduction(), function (Builder $query) use ($order) {
                        $query->whereNot('id', $order->orderable->wb_market_id);
                    })
                    ->whereHas('items', function (Builder $query) use ($order) {
                        $query->where('item_id', $order->orderable->item_id);
                    })
                    ->get();

                $markets->each(function (WbMarket $market) use ($order) {

                    $wbItem = $market->items()->where('item_id', $order->orderable->item_id)->first();

                    $market->warehouses->each(function (WbWarehouse $warehouse) use ($order, $wbItem) {
                        $warehouse->stocks()->where('wb_item_id', $wbItem->id)->each(function (WbWarehouseStock $stock) use ($order) {
                            $total = $stock->stock - $order->count + ($order->writeOffStocks()->first() ? $order->writeOffStocks()->first()->stock : 0);
                            $stock->stock = max(0, $total);
                            $stock->save();
                        });
                    });

                    $service = new WbItemPriceService(null, $market);
                    $service->unloadWbItemStocks($wbItem);
                });

                $order->markWriteOff();
            });
        });
    }
}
