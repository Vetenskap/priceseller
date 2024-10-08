<?php

namespace Modules\SamsonApi\Services;

use App\Models\Item;
use App\Models\User;
use App\Services\Item\ItemPriceService;
use Illuminate\Support\Str;
use Modules\SamsonApi\HttpClient\Resources\Sku;
use Modules\SamsonApi\HttpClient\Resources\SkuList;
use Modules\SamsonApi\Models\SamsonApi;
use Modules\SamsonApi\Models\SamsonApiItemAdditionalAttributeLink;

class SamsonUnloadService
{
    public SamsonApi $samsonApi;
    public User $user;

    /**
     * @param SamsonApi $samsonApi
     */
    public function __construct(SamsonApi $samsonApi)
    {
        $this->samsonApi = $samsonApi;
        $this->user = $samsonApi->user;
        $this->nullUpdated();
    }

    public function getNewPrice(): void
    {
        $skuList = new SkuList($this->samsonApi->api_key);

        do {

            $items = $skuList->fetchNext();

            $items->each(function (Sku $samsonItem) {

                $price = $samsonItem->getContract();
                $article = $samsonItem->getSku();
                $brand = $samsonItem->getBrand();
                $count = $samsonItem->getIdp();

                $itemService = new ItemPriceService($article, $this->samsonApi->supplier_id);
                $items = $this->samsonApi->supplier->use_brand ? $itemService->withBrand($brand)->find() : $itemService->find();

                if ($items) {

                    /** @var Item $item */
                    foreach ($items as $item) {

                        if ($count >= 0) {
                            $item->supplierWarehouseStocks()->updateOrCreate([
                                'supplier_warehouse_id' => $this->samsonApi->supplier_warehouse_id,
                                'item_id' => $item->id
                            ], [
                                'supplier_warehouse_id' => $this->samsonApi->supplier_warehouse_id,
                                'stock' => $count
                            ]);
                        }

                        $item->price = $price;
                        $item->updated = true;
                        $item->save();

                        $this->samsonApi->itemAdditionalAttributeLinks->each(function (SamsonApiItemAdditionalAttributeLink $link) use ($item, $samsonItem) {

                            $value = $samsonItem->{'get' . Str::apa($link->link)}();

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

        } while ($skuList->hasNext());
    }

    protected function nullUpdated(): void
    {
        $this->samsonApi->supplier->items()->update(['updated' => false]);
    }
}
