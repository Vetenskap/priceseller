<?php

namespace Modules\Assembly\Services;

use App\HttpClient\WbClient\Resources\Supply;
use App\Models\WbMarket;
use Illuminate\Support\Collection;
use Modules\Assembly\Models\AssemblyWbSupply;

class AssemblyWbService
{
    public static function createSupply(WbMarket $market, string $name, Collection $orders): void
    {
        $supply = new Supply();
        $supply->setName($name);
        $supply->create($market->api_key)->fetch($market->api_key);
        $orders->each(fn (string $order_id) => $supply->addOrder($order_id, $market->api_key));

        $market->supplies()->create(array_merge($supply->toModel(), ['count_orders' => $orders->count()]));
    }

    public static function getSupply(AssemblyWbSupply $supplyModel): Supply
    {
        $supply = new Supply();
        $supply->setId($supplyModel->id_supply);
        $supply->fetch($supplyModel->market->api_key);
        $supply->fetchOrders($supplyModel->market->api_key);
        return $supply;
    }

    public static function closeSupply(Supply $supply, AssemblyWbSupply $supplyModel): bool
    {
        $status = $supply->close($supplyModel->market->api_key);

        if ($status) {
            $supply->fetch($supplyModel->market->api_key);
            $supplyModel->update($supply->toModel());
        }

        return $status;
    }
}
