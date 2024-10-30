<?php

namespace App\HttpClient\OzonClient\Resources;

use App\HttpClient\OzonClient\OzonClient;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;

class ProductInfoAttribute
{
    const ENDPOINT = '/v3/products/info/attributes';

    const ATTRIBUTES = [
        ['name' => 'height', 'label' => 'Высота упаковки'],
        ['name' => 'depth', 'label' => 'Глубина'],
        ['name' => 'width', 'label' => 'Ширина упаковки'],
        ['name' => 'dimension_unit', 'label' => 'Единица измерения габаритов'],
        ['name' => 'weight', 'label' => 'Вес товара в упаковке'],
        ['name' => 'weight_unit', 'label' => 'Единица измерения веса'],
        ['name' => 'description', 'label' => 'Описание товара'],
    ];

    protected int $id;
    protected string $barcode;
    protected string $category_id;
    protected string $name;
    protected string $offer_id;
    protected int $height;
    protected int $depth;
    protected int $width;
    protected string $dimension_unit;
    protected int $weight;
    protected string $weight_unit;
    protected string $description;

    protected Collection $images;

    public function setProductInfoAttribute(Collection $productInfoAttribute): void
    {
        $this->id = $productInfoAttribute->get('id');
        $this->barcode = $productInfoAttribute->get('barcode');
        $this->category_id = $productInfoAttribute->get('category_id');
        $this->name = $productInfoAttribute->get('name');
        $this->offer_id = $productInfoAttribute->get('offer_id');
        $this->height = $productInfoAttribute->get('height');
        $this->depth = $productInfoAttribute->get('depth');
        $this->width = $productInfoAttribute->get('width');
        $this->dimension_unit = $productInfoAttribute->get('dimension_unit');
        $this->weight = $productInfoAttribute->get('weight');
        $this->weight_unit = $productInfoAttribute->get('weight_unit');
        $this->images = $productInfoAttribute->get('images');
    }

    public function fetch(OzonMarket $market, string $productId = null, string $offerId = null): void
    {
        $data = [
            "filter" => [
                "product_id" => $productId ? [$productId]: [],
                "offer_id" => $offerId ? [$offerId] : [],
                "visibility" => "ALL"
            ],
            "limit" => 1000,
            "last_id" => "",
            "sort_dir" => "ASC"
        ];

        $ozonClient = new OzonClient($market->api_key, $market->client_id);

        $response = $ozonClient->post(self::ENDPOINT, $data)->collect()->toCollectionSpread()->get('result')->first();
        $description = $ozonClient->post('/v1/product/info/description', [
            "offer_id" => $offerId,
            "product_id" => $productId
        ])->collect()->toCollectionSpread()->get('result');

        $this->description = $description->get('description');

        $this->setProductInfoAttribute($response);
    }


    public function toCollection(): Collection
    {
        return collect([
            'id' => $this->id,
            'barcode' => $this->barcode,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'offer_id' => $this->offer_id,
            'height' => $this->height,
            'depth' => $this->depth,
            'width' => $this->width,
            'dimension_unit' => $this->dimension_unit,
            'weight' => $this->weight,
            'weight_unit' => $this->weight_unit,
            'images' => $this->images->toArray(),
            'description' => $this->description
        ]);
    }
}
