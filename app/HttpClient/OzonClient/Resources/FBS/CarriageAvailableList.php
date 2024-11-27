<?php

namespace App\HttpClient\OzonClient\Resources\FBS;

use App\HttpClient\OzonClient\OzonClient;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class CarriageAvailableList implements Wireable
{
    const ENDPOINT = '/v1/posting/carriage-available/list';

    protected ?int $carriage_id = null;
    protected ?int $carriage_postings_count = null;
    protected ?string $warehouse_name = null;
    protected ?ActGetBarcode $actBarcode = null;

    public function __construct(Collection $carriage)
    {
        $this->carriage_id = $carriage->get('carriage_id');
        $this->carriage_postings_count = $carriage->get('carriage_postings_count');
        $this->warehouse_name = $carriage->get('warehouse_name');
        if ($carriage->has('actBarcode')) {
            $this->actBarcode = new ActGetBarcode($carriage->get('actBarcode'));
        }
    }

    public function fetchActBarcode(string $apiKey, int $clientId): void
    {
        $this->actBarcode = ActGetBarcode::fetch($apiKey, $clientId, $this->carriage_id);
    }

    public static function getAll(string $apiKey, int $clientId, string $departureDate = null, int $deliveryMethodId = 0): Collection
    {
        if (!$departureDate) {
            $departureDate = now()->endOfDay()->toRfc3339String();
        }

        $client = new OzonClient($apiKey, $clientId);
        $response = $client->post(static::ENDPOINT, [
            'delivery_method_id' => $deliveryMethodId,
            'departure_date' => $departureDate
        ]);

        return $response->collect('result')->toCollectionSpread()->map(fn (Collection $carriage) => new self($carriage));
    }

    public function getCarriageId(): ?int
    {
        return $this->carriage_id;
    }

    public function getCarriagePostingsCount(): ?int
    {
        return $this->carriage_postings_count;
    }

    public function getWarehouseName(): ?string
    {
        return $this->warehouse_name;
    }

    public function getActBarcode(): ?ActGetBarcode
    {
        return $this->actBarcode;
    }

    public function toLivewire(): array
    {
        return [
            'carriage_id' => $this->carriage_id,
            'carriage_postings_count' => $this->carriage_postings_count,
            'warehouse_name' => $this->warehouse_name,
            'actBarcode' => $this->actBarcode?->toLivewire(),
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(collect($value)->toCollectionSpread());
    }
}
