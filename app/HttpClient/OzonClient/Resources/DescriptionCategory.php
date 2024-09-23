<?php

namespace App\HttpClient\OzonClient\Resources;

use App\HttpClient\OzonClient\OzonClient;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DescriptionCategory
{
    protected ?int $description_category_id = null;

    protected ?string $category_name = null;

    protected bool $disabled;

    protected ?string $type_name = null;

    protected ?int $type_id = null;

    protected DescriptionCategory|DescriptionCategoryTree|null $children = null;

    protected ?Collection $attributes = null;

    public function setDescriptionCategory(Collection $descriptionCategory): void
    {
        if ($descriptionCategory->get('children')->isEmpty()) {
            $this->type_name = $descriptionCategory->get('type_name');
            $this->type_id = $descriptionCategory->get('type_id');
        } else {
            $this->description_category_id = $descriptionCategory->get('description_category_id');
            $this->category_name = $descriptionCategory->get('category_name');
            $this->disabled = $descriptionCategory->get('disabled');
        }

        if ($descriptionCategory->get('children')->isNotEmpty()) {
            if (!$descriptionCategory->get('children')->get('disabled') && $descriptionCategory->get('children')->get(0)) {
                $newDescriptionCategoryTree = new DescriptionCategoryTree();
                $newDescriptionCategoryTree->setDescriptionCategoryTree($descriptionCategory->get('children'));
                $this->children = $newDescriptionCategoryTree;
            } else {
                $newDescriptionCategory = new DescriptionCategory();
                $newDescriptionCategory->setDescriptionCategory($descriptionCategory->get('children'));
                $this->children = $newDescriptionCategory;
            }
        }
    }

    public function fetchAttributes(OzonMarket $market, int $descriptionCategoryId): void
    {
        $client = new OzonClient($market->api_key, $market->client_id);

        if (!$this->type_id) {
            throw new \Exception('this class is not a description category tree');
        }

        $data = [
            'description_category_id' => $descriptionCategoryId,
            'type_id' => $this->getTypeId(),
            'language' => 'DEFAULT',
        ];

        $result = Cache::tags(['ozon', 'market', 'description', 'category', 'attribute'])->remember(json_encode($data), now()->addDay(), fn() => $client->post(DescriptionCategoryAttribute::ENDPOINT, $data))->toCollectionSpread()->get('result');
        $attributes = new Collection();
        $result->each(function (Collection $attribute) use ($attributes) {
            $newAttribute = new DescriptionCategoryAttribute();
            $newAttribute->setDescriptionCategoryAttribute($attribute);
            $attributes->push($newAttribute);
        });
        $this->attributes = $attributes;
    }

    public function getDescriptionCategoryId(): int
    {
        return $this->description_category_id;
    }

    public function getCategoryName(): string
    {
        return $this->category_name;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getTypeName(): ?string
    {
        return $this->type_name;
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function getChildren(): DescriptionCategory|DescriptionCategoryTree|null
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return boolval($this->children);
    }

    public function getAttributes(): ?Collection
    {
        return $this->attributes;
    }

}
