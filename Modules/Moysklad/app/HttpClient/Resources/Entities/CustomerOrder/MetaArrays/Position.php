<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\CustomerOrder\MetaArrays;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;

class Position
{
    protected string $id;
    protected string $accountId;
    protected int $quantity;
    protected float $price;
    protected float $discount;
    protected int $vat;
    protected bool $vatEnabled;
    protected Product $assortment;
    protected float $shipped;
    protected float $reserve;

    public function __construct(Collection $position)
    {
        $this->id = $position->get('id');
        $this->accountId = $position->get('accountId');
        $this->quantity = $position->get('quantity');
        $this->price = $position->get('price');
        $this->discount = $position->get('discount');
        $this->vat = $position->get('vat');
        $this->vatEnabled = $position->get('vatEnabled');
        $this->shipped = $position->get('shipped');
        $this->reserve = $position->get('reserve');

        $product = new Product();
        $product->setId(collect($position->get('assortment'))->toCollectionSpread()->get('meta')->get('href'));
        $this->assortment = $product;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getVat(): int
    {
        return $this->vat;
    }

    public function isVatEnabled(): bool
    {
        return $this->vatEnabled;
    }

    public function getAssortment(): Product
    {
        return $this->assortment;
    }

    public function getShipped(): float
    {
        return $this->shipped;
    }

    public function getReserve(): float
    {
        return $this->reserve;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'accountId' => $this->accountId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'vat' => $this->vat,
            'vatEnabled' => $this->vatEnabled,
            'assortment' => $this->assortment,
            'shipped' => $this->shipped,
            'reserve' => $this->reserve
        ];
    }


}
