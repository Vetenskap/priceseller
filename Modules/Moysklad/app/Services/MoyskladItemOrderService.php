<?php

namespace Modules\Moysklad\Services;

use App\Models\Item;
use Illuminate\Support\Collection;
use Modules\Moysklad\Models\MoyskladItemOrder;

class MoyskladItemOrderService
{
    public static function getOrders(Item $item): Collection
    {
        $orders = $item->moyskladOrders()->where('new', true)->get();

        return $orders->filter(fn (MoyskladItemOrder $order) => static::checkOrder($order));

    }

    public static function checkOrder(MoyskladItemOrder $order): bool
    {
        if ($order->moysklad->clear_order_time) {
            if ($order->updated_at->addMinutes($order->moysklad->clear_order_time)->toDateTimeString() < now()->toDateTimeString()) {
                $order->update(['new' => false]);
                return false;
            }
        }

        return true;
    }
}
