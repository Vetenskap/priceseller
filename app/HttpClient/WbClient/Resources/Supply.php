<?php

namespace App\HttpClient\WbClient\Resources;

use App\HttpClient\WbClient\WbClient;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class Supply implements Wireable
{
    const ENDPOINT = 'https://marketplace-api.wildberries.ru/api/v3/supplies';

    protected ?string $id;
    protected ?string $name;
    protected ?bool $done;
    protected ?string $createdAt;
    protected ?string $closedAt;
    protected ?string $scanDt;
    protected ?int $cargoType;
    protected ?Collection $orders;

    public function close(string $api_key): bool
    {
        $client = new WbClient($api_key);
        $response = $client->patch(self::ENDPOINT . '/' . $this->id . '/deliver');
        return $response->successful();
    }

    public function fetchOrders(string $api_key): void
    {
        $client = new WbClient($api_key);
        $response = $client->get(self::ENDPOINT . '/' . $this->id . '/orders');
        $this->orders = $response->collect()->toCollectionSpread()->get('orders')->map(fn (Collection $order) => new Order($order));
    }

    public function fetch(string $api_key): void
    {
        $client = new WbClient($api_key);
        $response = $client->get(self::ENDPOINT . '/' . $this->id);
        $this->setSupply($response->collect()->toCollectionSpread());
    }

    public function addOrder(int $order_id, string $api_key): bool
    {
        $client = new WbClient($api_key);
        $response = $client->patch(self::ENDPOINT . '/' . $this->id . '/orders/' . $order_id);
        return $response->successful();
    }

    public function create(string $api_key): Supply
    {
        $data = [
            'name' => $this->name
        ];

        $client = new WbClient($api_key);
        $response = $client->post(self::ENDPOINT, $data);

        $this->setSupply($response->collect()->toCollectionSpread());

        return $this;
    }

    public function __construct(Collection $supply = null)
    {
        if ($supply) {
            $this->setSupply($supply);
        }
    }

    public function toModel(): array
    {
        return [
            'name' => $this->name,
            'id_supply' => $this->id,
            'created_at' => $this->createdAt,
            'closed_at' => $this->closedAt,
            'scan_dt' => $this->scanDt,
            'cargo_type' => $this->cargoType,
            'done' => $this->done
        ];
    }

    public function setSupply(Collection $supply): void
    {
        $this->id = $supply->get('id');
        $this->name = $supply->get('name');
        $this->done = $supply->get('done');
        $this->createdAt = $supply->get('createdAt');
        $this->closedAt = $supply->get('closedAt');
        $this->scanDt = $supply->get('scanDt');
        $this->cargoType = $supply->get('cargoType');
        if ($supply->has('orders')) {
            $this->orders = $supply->get('orders');
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getClosedAt(): string
    {
        return $this->closedAt;
    }

    public function getScanDt(): string
    {
        return $this->scanDt;
    }

    public function getCargoType(): int
    {
        return $this->cargoType;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'done' => $this->done,
            'createdAt' => $this->createdAt,
            'closedAt' => $this->closedAt,
            'scanDt' => $this->scanDt,
            'cargoType' => $this->cargoType,
            'orders' => $this->orders->map(fn (Order $order) => $order->toLivewire())
        ];
    }

    public static function fromLivewire($value): Supply
    {
        $data = collect($value)->toCollectionSpread();
        $data['orders'] = $data->get('orders')->map(fn (Collection $order) => new Order($order));

        return new static($data);
    }

}
