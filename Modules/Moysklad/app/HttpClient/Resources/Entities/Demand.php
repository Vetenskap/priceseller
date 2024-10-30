<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\CustomerOrder;

class Demand extends Entity
{
    const ENDPOINT = '/entity/demand/';
    protected CustomerOrder $customerOrder;

    public function __construct(?Collection $demand = null)
    {
        if ($demand) {
            $this->set($demand);
        }
    }

    protected function set(Collection $demand): void
    {
        $this->data = $demand;
        $this->id = $demand->get('id');

        if ($demand->has('customerOrder')) {
            $customerOrder = new CustomerOrder();
            $customerOrder->setId(collect($demand->get('customerOrder'))->toCollectionSpread()->get('meta')->get('href'));
            $this->customerOrder = $customerOrder;
        }
    }

    public function getCustomerOrder(): CustomerOrder
    {
        return $this->customerOrder;
    }


}
