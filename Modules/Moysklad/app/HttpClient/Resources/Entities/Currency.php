<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;

class Currency extends Entity
{
    const ENDPOINT = '/entity/currency/';

    protected bool $archived;
    protected string $code;
    protected bool $default;
    protected ?string $fullName;
    protected bool $indirect;
    protected string $isoCode;

    protected ?float $margin;

    protected int $multiplicity;
    protected string $name;
    protected float $rate;
    protected string $rateUpdateType;
    protected bool $system;

    public function __construct(?Collection $currency = null)
    {
        if ($currency) {

            $this->set($currency);

        }
    }

    protected function set(Collection $currency): void
    {
        $this->archived = $currency->get('archived');
        $this->code = $currency->get('code');
        $this->default = $currency->get('default');
        $this->fullName = $currency->get('fullName');
        $this->id = $currency->get('id');
        $this->indirect = $currency->get('indirect');
        $this->isoCode = $currency->get('isoCode');
        $this->margin = $currency->get('margin');
        $this->multiplicity = $currency->get('multiplicity');
        $this->name = $currency->get('name');
        $this->rate = $currency->get('rate');
        $this->rateUpdateType = $currency->get('rateUpdateType');
        $this->system = $currency->get('system');
    }


}