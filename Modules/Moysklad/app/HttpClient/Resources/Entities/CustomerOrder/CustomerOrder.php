<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\MetaArrays\Position;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;

class CustomerOrder extends Entity
{
    const ENDPOINT = '/entity/customerorder/';

    public Collection $positions;

    public function __construct(?Collection $customerOrder = null)
    {
        if ($customerOrder) {
            $this->set($customerOrder);
        }
    }

    public function set(Collection $customerOrder): void
    {
        $this->data = $customerOrder;
        $this->id = $customerOrder->get('id');
    }

    public function fetchPositions(string $apiKey): void
    {
        $client = new MoyskladClient($apiKey);
        $result = $client->get(self::ENDPOINT . $this->id . '/positions');

        $this->positions = collect();

        collect($result->get('rows'))->each(function (array $row) {

            $this->positions->push(new Position(collect($row)));

        });
    }

    public function getPositions(): Collection
    {
        return $this->positions;
    }


}
