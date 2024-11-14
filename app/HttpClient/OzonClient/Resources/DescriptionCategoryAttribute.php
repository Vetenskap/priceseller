<?php

namespace App\HttpClient\OzonClient\Resources;

use App\HttpClient\OzonClient\OzonClient;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;
use Opcodes\LogViewer\Facades\Cache;

class DescriptionCategoryAttribute
{
    const ENDPOINT = '/v1/description-category/attribute';

    protected bool $category_dependent;
    protected string $description;
    protected int $dictionary_id;
    protected int $group_id;
    protected string $group_name;
    protected int $id;
    protected bool $is_aspect;
    protected bool $is_collection;
    protected bool $is_required;
    protected string $name;
    protected string $type;
    protected int $attribute_complex_id;
    protected int $max_value_count;

    protected ?Collection $values = null;

    public function setDescriptionCategoryAttribute(Collection $descriptionCategoryAttribute): void
    {
        $this->category_dependent = $descriptionCategoryAttribute->get('category_dependent');
        $this->description = $descriptionCategoryAttribute->get('description');
        $this->dictionary_id = $descriptionCategoryAttribute->get('dictionary_id');
        $this->group_id = $descriptionCategoryAttribute->get('group_id');
        $this->group_name = $descriptionCategoryAttribute->get('group_name');
        $this->id = $descriptionCategoryAttribute->get('id');
        $this->is_aspect = $descriptionCategoryAttribute->get('is_aspect');
        $this->is_collection = $descriptionCategoryAttribute->get('is_collection');
        $this->is_required = $descriptionCategoryAttribute->get('is_required');
        $this->name = $descriptionCategoryAttribute->get('name');
        $this->type = $this->switchType($descriptionCategoryAttribute->get('type'));
        $this->attribute_complex_id = $descriptionCategoryAttribute->get('attribute_complex_id');
        $this->max_value_count = $descriptionCategoryAttribute->get('max_value_count');
    }

    public function switchType(string $type): string
    {
        return match ($type) {
            'Integer', 'Decimal' => 'number',
            'multiline' => 'textarea',
            default => 'text',
        };
    }

    public function fetchValues(OzonMarket $market, int $descriptionCategoryId, int $typeId, string $value): void
    {
        if ($this->dictionary_id) {

            $values = new Collection();

            $data = [
                'attribute_id' => $this->id,
                'description_category_id' => $descriptionCategoryId,
                'type_id' => $typeId,
                'limit' => 100,
                'value' => $value
            ];

            $client = new OzonClient($market->api_key, $market->client_id);
            $result = Cache::tags(['ozon', 'market', 'description', 'category', 'attribute', 'value'])->remember(json_encode($data), now()->addDay(), fn() => $client->post(DescriptionCategoryAttributeValue::ENDPOINT, $data)->collect())->toCollectionSpread();

            $dictionary = $result->get('result');

            $dictionary->each(function (Collection $value) use ($values) {
                $newValue = new DescriptionCategoryAttributeValue();
                $newValue->setDescriptionCategoryAttributeValue($value);
                $values->push($newValue);
            });


            $this->values = $values;

        }
    }

    public function isCategoryDependent(): bool
    {
        return $this->category_dependent;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDictionaryId(): int
    {
        return $this->dictionary_id;
    }

    public function getGroupId(): int
    {
        return $this->group_id;
    }

    public function getGroupName(): string
    {
        return $this->group_name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isIsAspect(): bool
    {
        return $this->is_aspect;
    }

    public function isIsCollection(): bool
    {
        return $this->is_collection;
    }

    public function isIsRequired(): bool
    {
        return $this->is_required;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getAttributeComplexId(): int
    {
        return $this->attribute_complex_id;
    }

    public function getMaxValueCount(): int
    {
        return $this->max_value_count;
    }

    public function getValues(): ?Collection
    {
        return $this->values;
    }

    public function toArray(): array
    {
        return [
            'category_dependent' => $this->category_dependent,
            'description' => $this->description,
            'dictionary_id' => $this->dictionary_id,
            'group_id' => $this->group_id,
            'group_name' => $this->group_name,
            'id' => $this->id,
            'is_aspect' => $this->is_aspect,
            'is_collection' => $this->is_collection,
            'is_required' => $this->is_required,
            'name' => $this->name,
            'type' => $this->type,
            'attribute_complex_id' => $this->attribute_complex_id,
            'max_value_count' => $this->max_value_count,
            'values' => $this->values ? $this->values->map(fn (DescriptionCategoryAttributeValue $value) => $value->toArray())->toArray() : [],
        ];
    }

}
