<?php

namespace Modules\BergApi\Services;

use App\Models\Item;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\BergApi\HttpClient\BergApiClient;
use Modules\BergApi\HttpClient\Resources\Offer;
use Modules\BergApi\HttpClient\Resources\Resource;
use Modules\BergApi\Models\BergApi;
use Modules\BergApi\Models\BergApiItemAdditionalAttributeLink;
use Modules\BergApi\Models\BergApiWarehouse;

class BergUnloadService
{
    public BergApi $bergApi;
    public User $user;

    /**
     * @param BergApi $bergApi
     */
    public function __construct(BergApi $bergApi)
    {
        $this->bergApi = $bergApi;
        $this->user = $bergApi->user;
        $this->nullUpdated();
    }

    public function getNewPrice(): void
    {
        $this->bergApi->supplier->items()->chunk(50, function (Collection $items) {

            $data = $items->map(function (Item $item) {
                return ['resource_article' => $item->article, 'brand_name' => $item->brand];
            });

            $client = new BergApiClient();
            $result = $client->get(Resource::ENDPOINT, [
                'key' => $this->bergApi->api_key,
                'items' => $data->toArray()
            ]);

            $resources = $result->toCollectionSpread()->get('resources')->map(function (Collection $resource) {
                return new Resource($resource);
            });

            /** @var Resource $resource */
            foreach ($resources as $resource) {

                $itemService = new ItemPriceService($resource->getArticle(), $this->bergApi->supplier_id);
                $items = $this->bergApi->supplier->use_brand ? $itemService->withBrand($resource->getBrandName())->find() : $itemService->find();
                $price = $resource->getOffers()->firstWhere(fn (Offer $offer) => in_array($offer->getWarehouseId(), $this->bergApi->warehouses->pluck('warehouse_id')->toArray()))?->getPrice();

                if ($items && $price) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        $this->bergApi->warehouses()->each(function (BergApiWarehouse $warehouse) use ($resource, $item) {
                            /** @var Offer $offer */
                            $offer = $resource->getOffers()->firstWhere(fn (Offer $offer) => $offer->getWarehouseId() == $warehouse->warehouse_id);

                            if ($offer) {
                                $stock = $offer->getQuantity();

                                if ($stock >= 0) {
                                    $item->supplierWarehouseStocks()->updateOrCreate([
                                        'supplier_warehouse_id' => $warehouse->supplier_warehouse_id,
                                        'item_id' => $item->id
                                    ], [
                                        'supplier_warehouse_id' => $warehouse->supplier_warehouse_id,
                                        'stock' => $stock
                                    ]);
                                }
                            }
                        });

                        $item->price = $price;
                        $item->updated = true;
                        $item->save();

                        $this->bergApi->itemAdditionalAttributeLinks->each(function (BergApiItemAdditionalAttributeLink $link) use ($item, $resource) {

                            $value = $resource->{'get' . Str::apa($link->link)}();

                            if (!is_null($value)) {
                                $item->attributesValues()->updateOrCreate([
                                    'item_attribute_id' => $link->item_attribute_id
                                ], [
                                    'item_attribute_id' => $link->item_attribute_id,
                                    'value' => $value,
                                ]);
                            }
                        });

                    }
                }

            }

        });

    }

    protected function nullUpdated(): void
    {
        $this->bergApi->supplier->items()->update(['updated' => false]);
    }
}
