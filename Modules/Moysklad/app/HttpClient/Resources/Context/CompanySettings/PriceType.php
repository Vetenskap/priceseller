<?php

namespace Modules\Moysklad\HttpClient\Resources\Context\CompanySettings;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;
use Modules\Moysklad\HttpClient\Resources\Entities\Entity;

class PriceType extends Entity
{
    const ENDPOINT = '/context/companysettings/pricetype/';

    protected string $name;
    protected string $externalCode;

    public function __construct(?Collection $priceType = null)
    {
        if ($priceType) {
            $this->set($priceType);
        }
    }

    public static function fetchAll(string $api_key)
    {
        $client = new MoyskladClient($api_key);

        $result = $client->get(self::ENDPOINT);

        return $result->map(function (array $priceType) {
            return new PriceType(collect($priceType));
        });
    }

    protected function set(Collection $priceType): void
    {
        $this->data = $priceType;
        $this->name = $priceType->get('name');
        $this->externalCode = $priceType->get('externalCode');
        $this->id = $priceType->get('id');
    }

    public function getMeta(): array
    {
        return [
            "meta" => [
                "href" => MoyskladClient::BASEURL . self::ENDPOINT . $this->id,
                "type" => "pricetype",
                "mediaType" => "application/json"
            ]
        ];
    }
}
