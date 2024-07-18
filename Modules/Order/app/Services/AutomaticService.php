<?php

namespace Modules\Order\Services;

use App\Models\Organization;
use App\Models\User;
use App\Models\Warehouse;

class AutomaticService
{

    public function __construct(public User $user)
    {
    }

    public function processOrders()
    {
        $this->user->organizations()->with('automaticUnloadOrder')->get()->where('automaticUnloadOrder.automatic', true)->each(function (Organization $organization) {
            $service = new OrderService($organization->id, $this->user);
            $service->getOrders();
            $service->writeOffBalance($organization->selectedOrdersWarehouses->map(fn (Warehouse $warehouse) => $warehouse->id)->toArray());

            $ozonService = new OzonOrderService($organization, $this->user);
            $ozonService->writeOffStocks();

            $wbService = new WbOrderService($organization, $this->user);
            $wbService->writeOffStocks();
        });
    }
}
