<?php

namespace App\Services\Item;

use App\Models\Item;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ItemPriceWithCacheService
{
    protected string $article;
    protected string $supplierId;
    protected string $brand = '';

    public function __construct(string $article, string $supplierId)
    {
        $this->article = $article;
        $this->supplierId = $supplierId;
    }

    public function withBrand(string $brand): ItemPriceWithCacheService
    {
        $this->brand = $brand;

        return $this;
    }
    public function find(): ?Item
    {
        return Cache::rememberForever($this->supplierId . '_' . $this->article . '_' . $this->brand, function () {
            return Item::query()
                ->where('supplier_id', $this->supplierId)
                ->where('article', $this->article)
                ->when($this->brand, fn (Builder $query) => $query->where('brand', $this->brand))
                ->first();
        });
    }

    public function save(Item $item)
    {
        $item->updated = true;
        $item->save();

        Cache::set($this->supplierId . '_' . $this->article . '_' . $this->brand, $item);
    }
}
