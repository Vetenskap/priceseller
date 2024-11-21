<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\Counterparty;
use Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\MetaArrays\Position;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;
use Modules\Moysklad\HttpClient\Resources\Entities\Project;
use Modules\Moysklad\HttpClient\Resources\Entities\Store;

class CustomerOrder extends Entity
{
    const ENDPOINT = '/entity/customerorder/';

    protected Collection $positions;
    protected ?Project $project = null;
    protected ?Store $store = null;
    protected Counterparty $agent;

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

        if ($customerOrder->has('positions')) {

            $this->positions = collect();

            $positions = collect($customerOrder->get('positions'));
            if ($positions->has('rows')) {
                collect($positions->get('rows'))->each(function (array $row) {

                    $this->positions->push(new Position(collect($row)));

                });
            }
        }

        if ($customerOrder->has('project')) {
            $project = new Project();
            $project->setId(collect($customerOrder->get('project'))->toCollectionSpread()->get('meta')->get('href'));
            $this->project = $project;
        }

        if ($customerOrder->has('store')) {
            $store = new Store();
            $store->setId(collect($customerOrder->get('store'))->toCollectionSpread()->get('meta')->get('href'));
            $this->store = $store;
        }

        $counterparty = new Counterparty();
        $counterparty->setId(collect($customerOrder->get('agent'))->toCollectionSpread()->get('meta')->get('href'));
        $this->agent = $counterparty;
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function getAgent(): Counterparty
    {
        return $this->agent;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'positions' => $this->positions?->map(fn (Position $position) => $position->toArray())->toArray(),
            'project' => $this->project?->toArray(),
            'store' => $this->store?->toArray(),
            'agent' => $this->agent->toArray(),
        ];
    }

}
