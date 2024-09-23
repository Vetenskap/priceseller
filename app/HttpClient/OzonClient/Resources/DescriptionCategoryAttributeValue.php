<?php

namespace App\HttpClient\OzonClient\Resources;

use Illuminate\Support\Collection;

class DescriptionCategoryAttributeValue
{
    const ENDPOINT = '/v1/description-category/attribute/values';

    protected int $id;

    protected string $info;

    protected string $picture;

    protected string $value;

    public function setDescriptionCategoryAttributeValue(Collection $descriptionCategoryAttributeValue): void
    {
        $this->id = $descriptionCategoryAttributeValue->get('id');
        $this->info = $descriptionCategoryAttributeValue->get('info');
        $this->picture = $descriptionCategoryAttributeValue->get('picture');
        $this->value = $descriptionCategoryAttributeValue->get('value');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInfo(): string
    {
        return $this->info;
    }

    public function getPicture(): string
    {
        return $this->picture;
    }

    public function getValue(): string
    {
        return $this->value;
    }

}
