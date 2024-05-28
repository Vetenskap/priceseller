<?php

namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;

class ItemPriceService
{
    protected ?string $article;
    protected string $supplierId;
    protected ?string $brand = '';

    public function __construct(?string $article, string $supplierId)
    {
        $this->article = $article;
        $this->supplierId = $supplierId;
    }

    public function withBrand(?string $brand): ItemPriceService
    {
        $this->brand = $brand;

        return $this;
    }
    public function find(): ?Item
    {
        return Item::query()
            ->where('supplier_id', $this->supplierId)
            ->where('article', $this->article)
            ->when($this->brand, fn(Builder $query) => $query->where('brand', $this->brand))
            ->first();
    }

    public function save(Item $item)
    {
        $item->save();
    }
}
