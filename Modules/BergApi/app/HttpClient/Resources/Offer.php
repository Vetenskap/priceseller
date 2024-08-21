<?php

namespace Modules\BergApi\HttpClient\Resources;

use Illuminate\Support\Collection;

class Offer
{
    protected int $warehouse_id;
    protected string $warehouse_name;
    protected int $warehouse_type;
    protected float $price;
    protected int $average_period;
    protected int $assured_period;
    protected int $reliability;
    protected bool $is_transit;
    protected int $quantity;
    protected bool $available_more;
    protected int $multiplication_factor;
    protected int $delivery_type;

    public function __construct(Collection $offer)
    {
        $this->warehouse_id = $offer->get('warehouse')->get('id');
        $this->warehouse_name = $offer->get('warehouse')->get('name');
        $this->warehouse_type = $offer->get('warehouse')->get('type');
        $this->price = $offer->get('price');
        $this->average_period = $offer->get('average_period');
        $this->assured_period = $offer->get('assured_period');
        $this->reliability = $offer->get('reliability');
        $this->is_transit = $offer->get('is_transit');
        $this->quantity = $offer->get('quantity');
        $this->available_more = $offer->get('available_more');
        $this->multiplication_factor = $offer->get('multiplication_factor');
        $this->delivery_type = $offer->get('delivery_type');
    }

    public function getWarehouseId(): int
    {
        return $this->warehouse_id;
    }

    public function getWarehouseName(): string
    {
        return $this->warehouse_name;
    }

    public function getWarehouseType(): int
    {
        return $this->warehouse_type;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getAveragePeriod(): int
    {
        return $this->average_period;
    }

    public function getAssuredPeriod(): int
    {
        return $this->assured_period;
    }

    public function getReliability(): int
    {
        return $this->reliability;
    }

    public function isIsTransit(): bool
    {
        return $this->is_transit;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function isAvailableMore(): bool
    {
        return $this->available_more;
    }

    public function getMultiplicationFactor(): int
    {
        return $this->multiplication_factor;
    }

    public function getDeliveryType(): int
    {
        return $this->delivery_type;
    }



}
