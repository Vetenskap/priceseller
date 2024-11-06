<?php

namespace Modules\Moysklad\HttpClient\Resources\Entities\Product\Metadata;

use Illuminate\Support\Collection;
use Modules\Moysklad\HttpClient\MoyskladClient;

class Attribute
{
    const ENDPOINT = '/entity/product/metadata/attributes/';

    protected string $id;
    protected string $name;
    protected string $type;
    protected bool|int|float|string|null $value;

    public function __construct(Collection $attribute)
    {
        $this->id = $attribute->get('id');
        $this->name = $attribute->get('name');
        $this->type = $attribute->get('type');
        $this->value = $attribute->get('value');
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValue(): float|bool|int|string|null
    {
        return $this->value;
    }

    public function getFieldProduct(): array
    {
        return [
            "meta" => [
                "href" => MoyskladClient::BASEURL . self::ENDPOINT . $this->id,
                "type" => "attributemetadata",
                "mediaType" => "application/json"
            ],
            "name" => $this->name,
            "value" => $this->value
        ];
    }


}
