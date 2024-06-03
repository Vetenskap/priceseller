<?php

namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
    public function find(): ?Collection
    {
        return Item::query()
            ->where('supplier_id', $this->supplierId)
            ->where('article', $this->article)
            ->when($this->brand, fn(Builder $query) => $query->where('brand', $this->brand))
            ->get();
    }

    public function save(Item $item)
    {
        $item->save();
    }
}
