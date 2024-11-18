<?php

namespace Modules\Moysklad\HttpClient\Resources\Objects;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\Resources\Entities\Currency;

class MinPrice
{
    protected ?float $value = null;
    protected ?Currency $currency = null;

    public function __construct(Collection $buyPrice)
    {
        $this->value = $buyPrice->get('value')/100;

        $currency = new Currency();
        $currency->setId(collect($buyPrice->get('currency'))->toCollectionSpread()->get('meta')->get('href'));
        $this->currency = $currency;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'currency' => $this->currency?->toArray(),
        ];
    }

}
