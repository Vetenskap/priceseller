<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use App\HttpClient\OzonClient\Resources\ProductInfoAttribute;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;

class Product
{
    const ATTRIBUTES = [
        ['name' => 'mandatory_mark', 'label' => 'Обязательная маркировка товара'],
        ['name' => 'name', 'label' => 'Название товара'],
        ['name' => 'offer_id', 'label' => 'Идентификатор товара в системе продавца — артикул'],
        ['name' => 'price', 'label' => 'Цена товара'],
        ['name' => 'quantity', 'label' => 'Количество товара в отправлении'],
        ['name' => 'sku', 'label' => 'Идентификатор товара в системе Ozon — SKU'],
        ['name' => 'currency_code', 'label' => 'Валюта ваших цен. Совпадает с валютой, которая установлена в настройках личного кабинета'],
    ];

    protected $mandatory_mark;
    protected $name;
    protected $offer_id;
    protected $price;
    protected $quantity;
    protected $sku;
    protected $currency_code;
    protected OzonItem $product;
    protected ProductInfoAttribute $attribute;

    public function __construct(Collection $product)
    {
        $this->mandatory_mark = $product->get('mandatory_mark');
        $this->name = $product->get('name');
        $this->offer_id = $product->get('offer_id');
        $this->price = $product->get('price');
        $this->quantity = $product->get('quantity');
        $this->sku = $product->get('sku');
        $this->currency_code = $product->get('currency_code');
    }

    public function toCollection(): Collection
    {
        return collect([
            'mandatory_mark' => $this->mandatory_mark,
            'name' => $this->name,
            'offer_id' => $this->offer_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'sku' => $this->sku,
            'currency_code' => $this->currency_code,
            'product' => $this->product,
            'attribute' => $this->attribute->toCollection(),
        ]);
    }

    public function loadLink(OzonMarket $market): void
    {
        $this->product = $market->items()->where('offer_id', $this->offer_id)->first();
    }

    public function fetchAttribute(OzonMarket $market): void
    {
        $attribute = new ProductInfoAttribute();
        $attribute->fetch($market, offerId: $this->offer_id);
        $this->attribute = $attribute;
    }
}
