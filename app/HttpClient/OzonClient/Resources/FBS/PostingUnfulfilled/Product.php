<?php

namespace App\HttpClient\OzonClient\Resources\FBS\PostingUnfulfilled;

use App\HttpClient\OzonClient\Resources\ProductInfoAttribute;
use App\Models\OzonItem;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;
use Livewire\Wireable;

class Product implements Wireable
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
        if ($product->has('product')) $this->product = $product->get('product');
        if ($product->has('attribute')) $this->attribute = new ProductInfoAttribute(collect($product->get('attribute'))->toCollectionSpread());
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

    public function toLivewire(): array
    {
        return [
            'mandatory_mark' => $this->mandatory_mark,
            'name' => $this->name,
            'offer_id' => $this->offer_id,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'sku' => $this->sku,
            'currency_code' => $this->currency_code,
            'product' => $this->product,
            'attribute' => $this->attribute->toLivewire(),
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(collect($value)->toCollectionSpread());
    }

    public function getMandatoryMark()
    {
        return $this->mandatory_mark;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getOfferId()
    {
        return $this->offer_id;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function getCurrencyCode()
    {
        return $this->currency_code;
    }

    public function getProduct(): OzonItem
    {
        return $this->product;
    }

    public function getAttribute(): ProductInfoAttribute
    {
        return $this->attribute;
    }

}
