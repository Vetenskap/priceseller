<?php

namespace App\HttpClient\OzonClient\Resources;

use App\HttpClient\OzonClient\OzonClient;
use App\Models\OzonMarket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DescriptionCategoryTree
{
    const ENDPOINT = '/v1/description-category/tree';

    protected Collection $descriptionCategories;

    public function fetch(OzonMarket $market): void
    {
        $client = new OzonClient($market->api_key, $market->client_id);
        $result = Cache::tags(['ozon', 'market', 'description_category_tree'])->remember('base', now()->addDay(), fn() => $client->post(self::ENDPOINT, [
            'language' => 'DEFAULT'
        ])->toCollectionSpread()->get('result'));

        $this->setDescriptionCategoryTree($result);
    }

    public function setDescriptionCategoryTree(Collection $descriptionCategoryTree): void
    {
        $descriptionCategories = new Collection();
        $descriptionCategoryTree->each(function (Collection $descriptionCategory) use ($descriptionCategories, $descriptionCategoryTree) {
            if (!$descriptionCategory->get('disabled') && $descriptionCategory->get(0)) {
                $newDescriptionCategoryTree = new DescriptionCategoryTree();
                $newDescriptionCategoryTree->setDescriptionCategoryTree($descriptionCategory);
                $descriptionCategories->push($newDescriptionCategoryTree);
            } else {
                $newDescriptionCategory = new DescriptionCategory();
                $newDescriptionCategory->setDescriptionCategory($descriptionCategory);
                $descriptionCategories->push($newDescriptionCategory);
            }
        });
        $this->descriptionCategories = $descriptionCategories;
    }

    public function getDescriptionCategories(): Collection
    {
        return $this->descriptionCategories;
    }

    public function toArray(): array
    {
        return $this->descriptionCategories->map(fn (DescriptionCategoryTree|DescriptionCategory $description) => $description->toArray())->toArray();
    }

}
