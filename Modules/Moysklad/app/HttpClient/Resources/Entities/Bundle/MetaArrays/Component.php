<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Bundle\MetaArrays;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Product\Product;

class Component
{
    protected ?Product $assortment = null;
    protected ?int $quantity = null;

    public function __construct(Collection $component)
    {
        $this->quantity = $component->get('quantity');

        $product = new Product();
        $product->setId(collect($component->get('assortment'))->toCollectionSpread()->get('meta')->get('href'));
        $this->assortment = $product;
    }

    public function getAssortment(): Product
    {
        return $this->assortment;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function toArray(): array
    {
        return [
            'assortment' => $this->assortment->toArray(),
            'quantity' => $this->quantity
        ];
    }

}
