<?php

namespace App\HttpClient\OzonClient\Resources;

use App\HttpClient\OzonClient\OzonClient;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;

class ProductInfoPrices
{
    const ENDPOINT = '/v4/product/info/prices';

    protected int $acquiring;
    protected Collection $commissions;
    protected string $offer_id;
    protected Collection $price;
    protected Collection $price_indexes;
    protected int $product_id;

    public function setProductInfoPrices(Collection $productInfoPrices): void
    {
        $this->acquiring = $productInfoPrices->get('acquiring');
        $this->commissions = $productInfoPrices->get('commissions');
        $this->offer_id = $productInfoPrices->get('offer_id');
        $this->price = $productInfoPrices->get('price');
        $this->price_indexes = $productInfoPrices->get('price_indexes');
        $this->product_id = $productInfoPrices->get('product_id');
    }

    public static function fetchAll(OzonMarket $market, array $productIds): Collection
    {
        $data = [
            "filter" => [
                "product_id" => $productIds,
                "offer_id" => [],
                "visibility" => "ALL"
            ],
            "limit" => 100,
            "last_id" => "",
            "sort_dir" => "ASC"
        ];

        $ozonClient = new OzonClient($market->api_key, $market->client_id);

        return $ozonClient->post(self::ENDPOINT, $data)->toCollectionSpread()->get('result')->get('items')->map(function (Collection $item) {
            $productInfoPrices = new self();
            $productInfoPrices->setProductInfoPrices($item);
            return $productInfoPrices;
        });

    }

    public function getAcquiring(): int
    {
        return $this->acquiring;
    }

    public function getCommissions(): Collection
    {
        return $this->commissions;
    }

    public function getOfferId(): string
    {
        return $this->offer_id;
    }

    public function getPrice(): Collection
    {
        return $this->price;
    }

    public function getPriceIndexes(): Collection
    {
        return $this->price_indexes;
    }

    public function getProductId(): int
    {
        return $this->product_id;
    }


}
