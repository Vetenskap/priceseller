<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class Currency extends Entity
{
    const ENDPOINT = '/entity/currency/';

    protected ?bool $archived = null;
    protected ?string $code = null;
    protected ?bool $default = null;
    protected ?string $fullName = null;
    protected ?bool $indirect = null;
    protected ?string $isoCode = null;

    protected ?float $margin = null;

    protected ?int $multiplicity = null;
    protected ?string $name = null;
    protected ?float $rate = null;
    protected ?string $rateUpdateType = null;
    protected ?bool $system = null;

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

    public function getMeta(): array
    {
        return [
            "meta" => [
                "href" => MoyskladClient::BASEURL . self::ENDPOINT . $this->id,
                "metadataHref" => MoyskladClient::BASEURL . self::ENDPOINT . 'metadata',
                "type" => "currency",
                "mediaType" => "application/json"
            ]
        ];
    }

    public function toArray(): array
    {
        return [
            'archived' => $this->archived,
            'code' => $this->code,
            'default' => $this->default,
            'fullName' => $this->fullName,
            'id' => $this->id,
            'indirect' => $this->indirect,
            'isoCode' => $this->isoCode,
            'margin' => $this->margin,
            'multiplicity' => $this->multiplicity,
            'name' => $this->name,
            'rate' => $this->rate,
            'rateUpdateType' => $this->rateUpdateType,
            'system' => $this->system
        ];
    }


}
