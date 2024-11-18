<?php

namespace Modules\Moysklad\HttpClient\Resources\Objects;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Context\CompanySettings\PriceType;
use Modules\Moysklad\HttpClient\Resources\Entities\Currency;

class SalePrice
{
    protected ?float $value = null;
    protected ?Currency $currency = null;
    protected ?PriceType $priceType = null;

    public function __construct(Collection $salePrice = null)
    {
        if ($salePrice) {
            $this->value = $salePrice->get('value') / 100;

            if ($salePrice->has('currency')) {
                $currency = new Currency();
                $currency->setId(collect($salePrice->get('currency'))->toCollectionSpread()->get('meta')->get('href'));
                $this->currency = $currency;
            }

            if ($salePrice->has('priceType')) {
                $priceType = new PriceType();
                $priceType->setId(collect($salePrice->get('priceType'))->toCollectionSpread()->get('meta')->get('href'));
                $this->priceType = $priceType;
            }
        }
    }

    public function getFieldProduct(): array
    {
        return [
            "value" => $this->value * 100,
            "currency" => $this->currency->getMeta(),
            "priceType" => $this->priceType->getMeta(),
        ];
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function setPriceType(PriceType $priceType): void
    {
        $this->priceType = $priceType;
    }

    public function getPriceType(): PriceType
    {
        return $this->priceType;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            "value" => $this->value * 100,
            "currency" => $this->currency?->toArray(),
            "priceType" => $this->priceType?->toArray()
        ];
    }


}
