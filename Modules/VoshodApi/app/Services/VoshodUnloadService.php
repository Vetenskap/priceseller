<?php

namespace Modules\VoshodApi\Services;

use App\Models\Item;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Illuminate\Support\Str;
use Modules\VoshodApi\HttpClient\Resources\ItemsPageList;
use Modules\VoshodApi\Models\VoshodApi;
use Modules\VoshodApi\Models\VoshodApiItemAdditionalAttributeLink;
use Modules\VoshodApi\Models\VoshodApiWarehouse;

class VoshodUnloadService
{
    public VoshodApi $voshodApi;
    public User $user;

    /**
     * @param VoshodApi $voshodApi
     */
    public function __construct(VoshodApi $voshodApi)
    {
        $this->voshodApi = $voshodApi;
        $this->user = $voshodApi->user;
        $this->nullUpdated();
    }

    public function getNewPrice(): void
    {
        $itemsPageList = new ItemsPageList(
            $this->voshodApi->api_key,
            $this->voshodApi->proxy_ip,
            $this->voshodApi->proxy_port,
            $this->voshodApi->proxy_login,
            $this->voshodApi->proxy_password
        );

        do {

            $items = $itemsPageList->fetchNext();

            $items->each(function (\Modules\VoshodApi\HttpClient\Resources\Item $voshodItem) {

                $price = $voshodItem->getPrice();
                $article = $voshodItem->getMog();
                $brand = $voshodItem->getOemBrand();
                $count = 0;

                $this->voshodApi->warehouses->each(function (VoshodApiWarehouse $warehouse) use ($voshodItem, &$count) {
                    $count += $voshodItem->{'get' . Str::apa($warehouse->name)}();
                });

                $itemService = new ItemPriceService($article, $this->voshodApi->supplier_id);
                $items = $this->voshodApi->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

                if ($items) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        $item->price = $price;
                        $item->count = $count;
                        $item->updated = true;
                        $item->save();

                        $this->voshodApi->itemAdditionalAttributeLinks->each(function (VoshodApiItemAdditionalAttributeLink $link) use ($item, $voshodItem) {

                            $value = $voshodItem->{'get' . Str::apa($link->link)}();

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

            });

        } while ($itemsPageList->hasNext());
    }

    protected function nullUpdated(): void
    {
        $this->voshodApi->supplier->items()->update(['updated' => false]);
    }
}